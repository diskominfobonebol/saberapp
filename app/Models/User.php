<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

// TAMBAHAN 2: Tambahkan "implements FilamentUser"
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'username',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            // Jika belum ada password yang diberikan, buat otomatis
            if (empty($user->password)) {
                // Bisa pakai password random
                $plainPassword = Str::random(8);

                // Simpan versi hash-nya ke database
                $user->password = Hash::make(env('DEFAULT_PASSWORD'));
            }
        });
    }

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class);
    }

    public function opd()
    {
        return $this->hasOne(Opd::class);
    }


    // TAMBAHAN 3: Method untuk otorisasi Filament
    public function canAccessPanel(Panel $panel): bool
    {
        if (env('APP_ENV') == 'production') {
            // Izinkan semua pengguna yang berhasil login untuk mengakses panel.
            return true;
        }
        return true;
    }
}
