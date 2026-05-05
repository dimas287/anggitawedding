@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Akun')

@push('head')
<style>
    .profile-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 28px;
        backdrop-filter: blur(12px);
        margin-bottom: 16px;
    }
    .profile-card h3 {
        font-size: 14px; font-weight: 700;
        letter-spacing: .05em; color: var(--text-1);
        margin-bottom: 20px;
        display: flex; align-items: center; gap: 8px;
    }
    .form-label {
        display: block; font-size: 12px; font-weight: 600;
        letter-spacing: .08em; text-transform: uppercase;
        color: var(--text-3); margin-bottom: 8px;
    }
    .form-input {
        width: 100%;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 11px 16px;
        font-size: 14px; color: var(--text-1);
        font-family: 'Inter', sans-serif;
        outline: none;
        transition: all .2s;
        -webkit-appearance: none;
    }
    .form-input:focus {
        border-color: rgba(201,168,76,.5);
        box-shadow: 0 0 0 3px rgba(201,168,76,.1);
        background: var(--surface);
    }
    .form-input::placeholder { color: var(--text-3); }

    .btn-save {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 11px 24px;
        background: linear-gradient(135deg, var(--gold), var(--purple));
        color: #fff; border: none; border-radius: 12px;
        font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif;
        cursor: pointer; transition: all .2s;
        box-shadow: 0 4px 16px rgba(201,168,76,.25);
    }
    .btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 24px rgba(201,168,76,.35); }

    .btn-outline {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 11px 24px;
        background: var(--surface-2);
        border: 1px solid var(--border);
        color: var(--text-2); border-radius: 12px;
        font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif;
        cursor: pointer; transition: all .2s;
    }
    .btn-outline:hover { border-color: rgba(201,168,76,.3); color: var(--gold); background: var(--surface); }

    .error-box {
        padding: 14px 16px;
        background: rgba(239,68,68,.08);
        border: 1px solid rgba(239,68,68,.2);
        border-radius: 12px; color: #f87171;
        font-size: 13px; margin-bottom: 16px;
    }
    .success-box {
        padding: 14px 16px;
        background: rgba(34,197,94,.08);
        border: 1px solid rgba(34,197,94,.2);
        border-radius: 12px; color: #4ade80;
        font-size: 13px; margin-bottom: 16px;
    }
    .form-group { margin-bottom: 18px; }
</style>
@endpush

@section('content')
<div style="max-width: 640px; margin: 0 auto;">

    {{-- Profile Header --}}
    <div class="profile-card" style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
        <div style="position:relative;">
            @php
                $uAvatar = $user->avatar ?? null;
                $uAvatarUrl = $uAvatar
                    ? (Str::startsWith($uAvatar, ['http://', 'https://']) ? $uAvatar : Storage::url($uAvatar))
                    : null;
                $uInitial = strtoupper(substr($user->name, 0, 1));
            @endphp

            @if($uAvatarUrl)
                <img src="{{ $uAvatarUrl }}" alt="{{ $user->name }}"
                     style="width:80px;height:80px;border-radius:20px;object-fit:cover;border:3px solid rgba(201,168,76,.35);"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div style="display:none;width:80px;height:80px;border-radius:20px;background:linear-gradient(135deg,var(--gold),var(--purple));align-items:center;justify-content:center;font-size:28px;font-weight:700;color:#fff;border:3px solid rgba(201,168,76,.35);">
                    {{ $uInitial }}
                </div>
            @else
                <div style="width:80px;height:80px;border-radius:20px;background:linear-gradient(135deg,var(--gold),var(--purple));display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;color:#fff;border:3px solid rgba(201,168,76,.35);">
                    {{ $uInitial }}
                </div>
            @endif
        </div>
        <div>
            <h2 class="font-playfair" style="font-size:22px;font-weight:700;color:var(--text-1);margin-bottom:4px;">
                {{ $user->name }}
            </h2>
            <p style="font-size:13px;color:var(--text-3);margin-bottom:8px;">{{ $user->email }}</p>
            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.2);border-radius:20px;font-size:11px;font-weight:600;color:var(--gold);">
                <i class="fas fa-user"></i> Klien
            </span>
        </div>
    </div>

    @if(session('success'))
    <div class="success-box"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
    @endif

    {{-- Edit Profile --}}
    <div class="profile-card">
        <h3><i class="fas fa-pen-to-square" style="color:var(--gold);"></i> Edit Profil</h3>

        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            @if($errors->any())
            <div class="error-box">
                @foreach($errors->all() as $e)<p><i class="fas fa-circle-exclamation mr-1"></i>{{ $e }}</p>@endforeach
            </div>
            @endif

            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input" placeholder="Nama lengkap Anda">
            </div>
            <div class="form-group">
                <label class="form-label">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input" placeholder="email@domain.com">
            </div>
            <div class="form-group">
                <label class="form-label">No. WhatsApp</label>
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" placeholder="08xxxxxxxxxx">
            </div>
            <div class="form-group">
                <label class="form-label">Alamat Lengkap</label>
                <textarea name="address" rows="3" class="form-input" placeholder="Jalan, Kelurahan, Kota, Provinsi" style="resize:vertical;">{{ old('address', $user->address) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Foto Profil</label>
                <div style="background:var(--surface-2);border:2px dashed var(--border);border-radius:12px;padding:16px;text-align:center;transition:.2s;">
                    <i class="fas fa-cloud-arrow-up" style="font-size:24px;color:var(--text-3);margin-bottom:8px;display:block;"></i>
                    <p style="font-size:12px;color:var(--text-3);margin-bottom:8px;">Pilih foto (maks. 2MB)</p>
                    <input type="file" name="avatar" accept="image/*"
                           style="font-size:12px;color:var(--text-2);"
                           onchange="previewAvatar(this)">
                    <img id="avatar-preview" src="" alt="" style="display:none;width:64px;height:64px;border-radius:16px;object-fit:cover;margin:10px auto 0;border:2px solid rgba(201,168,76,.3);">
                </div>
            </div>
            <button type="submit" class="btn-save">
                <i class="fas fa-floppy-disk"></i> Simpan Perubahan
            </button>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="profile-card">
        <h3><i class="fas fa-lock" style="color:var(--purple);"></i> Ubah Password</h3>
        <form action="{{ route('user.profile.password') }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Password Saat Ini</label>
                <input type="password" name="current_password" required class="form-input" placeholder="••••••••">
            </div>
            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" required minlength="8" class="form-input" placeholder="Min. 8 karakter">
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required class="form-input" placeholder="Ulangi password baru">
            </div>
            <button type="submit" class="btn-outline">
                <i class="fas fa-key"></i> Ubah Password
            </button>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
function previewAvatar(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('avatar-preview');
        img.src = e.target.result;
        img.style.display = 'block';
    };
    reader.readAsDataURL(file);
}
</script>
@endpush
