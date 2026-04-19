<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_code', 'user_id', 'package_id', 'groom_name', 'groom_short_name',
        'bride_name', 'bride_short_name',
        'event_date', 'venue', 'venue_address', 'phone', 'email',
        'estimated_guests', 'notes', 'consultation_preference',
        'package_price', 'dp_amount', 'client_notes',
    ];

    protected $casts = [
        'event_date' => 'date',
        'locked_at' => 'datetime',
        'is_locked' => 'boolean',
        'package_price' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'total_paid' => 'decimal:2',
    ];

    protected $appends = [
        'groom_display_name',
        'bride_display_name',
        'couple_short_display',
    ];

    public function getGroomDisplayNameAttribute(): string
    {
        return $this->formatDisplayName($this->groom_name, $this->groom_short_name);
    }

    public function getBrideDisplayNameAttribute(): string
    {
        return $this->formatDisplayName($this->bride_name, $this->bride_short_name);
    }

    public function getCoupleShortDisplayAttribute(): string
    {
        $groom = $this->groom_short_name ?: $this->groom_name;
        $bride = $this->bride_short_name ?: $this->bride_name;

        return trim($groom . ' & ' . $bride);
    }

    protected function formatDisplayName(?string $full, ?string $short): string
    {
        $full = trim((string) $full);
        $short = trim((string) $short);

        if ($short && strcasecmp($full, $short) !== 0) {
            return $full ? $full . ' (' . $short . ')' : $short;
        }

        return $full ?: $short ?: '-';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function extraCharges()
    {
        return $this->hasMany(BookingExtraCharge::class);
    }

    public function invitation()
    {
        return $this->hasOne(Invitation::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function vendors()
    {
        return $this->hasMany(BookingVendor::class);
    }

    public function rundowns()
    {
        return $this->hasMany(Rundown::class)->orderBy('sort_order')->orderBy('time');
    }

    public function financialTransactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function fittings()
    {
        return $this->hasMany(BookingFitting::class)->latest('scheduled_at');
    }

    public function wardrobeItems()
    {
        return $this->hasMany(BookingWardrobeItem::class)->latest();
    }

    public function scopeInvitationOnly($query)
    {
        return $query->where(function ($q) {
            $q->where('venue', 'Undangan Digital')
              ->orWhere(function ($sub) {
                  $sub->whereHas('invitation')
                      ->whereHas('package', function ($pkg) {
                          $pkg->where('includes_digital_invitation', false)
                              ->orWhereNull('includes_digital_invitation');
                      });
              });
        });
    }

    public function scopeWeddingPackages($query)
    {
        return $query->where(function ($q) {
            $q->where('venue', '!=', 'Undangan Digital')
              ->orWhereNull('venue');
        })->where(function ($q) {
            $q->whereDoesntHave('invitation')
              ->orWhereHas('package', function ($pkg) {
                  $pkg->where('includes_digital_invitation', true)
                      ->orWhereNull('includes_digital_invitation');
              });
        });
    }

    public function getActiveExtraChargesTotalAttribute(): float
    {
        /** @var Collection $charges */
        $charges = $this->relationLoaded('extraCharges')
            ? $this->extraCharges
            : $this->extraCharges()->get();

        return (float) $charges
            ->where('status', '!=', 'waived')
            ->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->package_price - $this->total_paid;
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'dp_paid' => 'Terbayar',
            'in_progress' => 'Sedang Berjalan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'dp_paid' => 'green',
            'in_progress' => 'indigo',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getTotalIncomeAttribute(): float
    {
        return $this->financialTransactions()->where('type', 'income')->sum('amount');
    }

    public function getTotalExpenseAttribute(): float
    {
        return $this->financialTransactions()->where('type', 'expense')->sum('amount');
    }

    public function getProfitAttribute(): float
    {
        return $this->total_income - $this->total_expense;
    }

    public function getIsInvitationOnlyAttribute(): bool
    {
        $packageHasInvitation = (bool) (optional($this->package)->includes_digital_invitation ?? false);
        $hasInvitation = (bool) $this->invitation;

        return $this->venue === 'Undangan Digital' || ($hasInvitation && !$packageHasInvitation);
    }

    public static function generateCode(): string
    {
        $prefix = 'WO';
        $year = date('Y');
        $count = self::withTrashed()->whereYear('created_at', $year)->count();

        do {
            $count++;
            $code = $prefix . $year . str_pad($count, 4, '0', STR_PAD_LEFT);
            $exists = self::withTrashed()->where('booking_code', $code)->exists();
        } while ($exists);

        return $code;
    }
}
