<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AdminActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'method',
        'route',
        'url',
        'ip_address',
        'user_agent',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    protected $appends = [
        'is_critical',
        'critical_label',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsCriticalAttribute(): bool
    {
        return $this->isDeleteAction() || $this->isStatusAction();
    }

    public function getCriticalLabelAttribute(): ?string
    {
        if ($this->isDeleteAction()) {
            return 'Penghapusan';
        }

        if ($this->isStatusAction()) {
            return 'Perubahan Status';
        }

        return $this->is_critical ? 'Aksi Kritis' : null;
    }

    protected function isDeleteAction(): bool
    {
        $action = Str::lower($this->action ?? '');

        return Str::contains($action, ['delete', 'destroy', 'hapus'])
            || strtoupper($this->method ?? '') === 'DELETE';
    }

    protected function isStatusAction(): bool
    {
        $action = Str::lower($this->action ?? '');

        return Str::contains($action, ['status', 'lock', 'unlock', 'approve', 'reject'])
            || Str::contains(Str::lower($this->route ?? ''), 'status');
    }
}
