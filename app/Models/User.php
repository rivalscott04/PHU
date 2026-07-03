<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\PengawasScopeMode;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Impersonate;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'travel_id',
        'cabang_id',
        'nama',
        'email',
        'nomor_hp',
        'password',
        'address',
        'city',
        'country',
        'postal',
        'about',
        'role',
        'kabupaten',
        'pengawas_scope',
        'pengawas_kabupatens',
        'is_password_changed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'pengawas_kabupatens' => 'array',
    ];

    public function travel()
    {
        return $this->belongsTo(TravelCompany::class, 'travel_id');
    }

    public function cabang()
    {
        return $this->belongsTo(\App\Models\CabangTravel::class, 'cabang_id', 'id_cabang');
    }

    /**
     * Get travel company name (either pusat or cabang)
     */
    public function getTravelCompanyName()
    {
        if ($this->travel) {
            return $this->travel->Penyelenggara . ' (Pusat)';
        } elseif ($this->cabang) {
            return $this->cabang->Penyelenggara . ' (Cabang)';
        }
        return 'Tidak ada travel';
    }

    /**
     * Get travel company badge class
     */
    public function getTravelCompanyBadgeClass()
    {
        if ($this->travel) {
            return 'bg-info';
        } elseif ($this->cabang) {
            return 'bg-warning';
        }
        return 'bg-secondary';
    }

    /**
     * Get kabupaten (either from pusat or cabang)
     */
    public function getKabupaten()
    {
        if ($this->travel) {
            return $this->travel->kab_kota;
        } elseif ($this->cabang) {
            return $this->cabang->kabupaten;
        }
        return $this->kabupaten;
    }

    public function pengawasScopeMode(): PengawasScopeMode
    {
        return PengawasScopeMode::tryFrom((string) $this->pengawas_scope)
            ?? PengawasScopeMode::Single;
    }

    /** @return list<string>|null Null means seluruh NTB (tanpa filter kabupaten). */
    public function getScopedKabupatens(): ?array
    {
        if ($this->role === UserRole::Admin->value || $this->role === UserRole::Pimpinan->value) {
            return null;
        }

        if ($this->role === UserRole::Kabupaten->value) {
            return $this->kabupaten ? [$this->kabupaten] : [];
        }

        if ($this->role !== UserRole::Pengawas->value) {
            return null;
        }

        return match ($this->pengawasScopeMode()) {
            PengawasScopeMode::All => null,
            PengawasScopeMode::Single => $this->kabupaten ? [$this->kabupaten] : [],
            PengawasScopeMode::Custom => array_values(array_filter($this->pengawas_kabupatens ?? [])),
        };
    }

    public function canAccessKabupaten(?string $kabupaten): bool
    {
        if (! $kabupaten) {
            return false;
        }

        if ($this->role === UserRole::Admin->value || $this->role === UserRole::Pimpinan->value) {
            return true;
        }

        if ($this->role === UserRole::Kabupaten->value) {
            return $kabupaten === $this->kabupaten;
        }

        if ($this->role !== UserRole::Pengawas->value) {
            return false;
        }

        $scoped = $this->getScopedKabupatens();

        return $scoped === null || in_array($kabupaten, $scoped, true);
    }

    public function getWilayahKerjaLabel(): string
    {
        if ($this->role === UserRole::Pimpinan->value) {
            return 'Seluruh NTB';
        }

        if ($this->role === UserRole::Pengawas->value) {
            return match ($this->pengawasScopeMode()) {
                PengawasScopeMode::All => 'Seluruh NTB',
                PengawasScopeMode::Single => $this->kabupaten ?? 'Tidak ada',
                PengawasScopeMode::Custom => implode(', ', $this->pengawas_kabupatens ?? []) ?: 'Tidak ada',
            };
        }

        return $this->getKabupaten() ?? 'Tidak ada';
    }

    public function scopePengawasForKabupaten(Builder $query, string $kabupaten): Builder
    {
        return $query->where('role', UserRole::Pengawas->value)
            ->where(function (Builder $scoped) use ($kabupaten): void {
                $scoped->where('pengawas_scope', PengawasScopeMode::All->value)
                    ->orWhere(function (Builder $single) use ($kabupaten): void {
                        $single->where(function (Builder $legacy) use ($kabupaten): void {
                            $legacy->where('pengawas_scope', PengawasScopeMode::Single->value)
                                ->orWhereNull('pengawas_scope');
                        })->where('kabupaten', $kabupaten);
                    })
                    ->orWhere(function (Builder $custom) use ($kabupaten): void {
                        $custom->where('pengawas_scope', PengawasScopeMode::Custom->value)
                            ->whereJsonContains('pengawas_kabupatens', $kabupaten);
                    });
            });
    }

    public function roleEnum(): ?UserRole
    {
        return UserRole::tryFromString($this->role);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin->value;
    }

    public function isPimpinan(): bool
    {
        return $this->role === UserRole::Pimpinan->value;
    }

    public function isPengawas(): bool
    {
        return $this->role === UserRole::Pengawas->value;
    }

    public function isKabupatenScoped(): bool
    {
        return in_array($this->role, [UserRole::Kabupaten->value, UserRole::Pengawas->value], true);
    }

    /**
     * Check if user can impersonate
     */
    public function canImpersonate()
    {
        return $this->isAdmin();
    }

    /** @return list<string> */
    public static function impersonatableRoles(): array
    {
        return [
            UserRole::Pimpinan->value,
            UserRole::Pengawas->value,
            UserRole::Kabupaten->value,
            UserRole::User->value,
        ];
    }

    /**
     * Check if user can be impersonated
     */
    public function canBeImpersonated(): bool
    {
        return in_array($this->role, self::impersonatableRoles(), true);
    }

    public function impersonationRedirectRoute(): string
    {
        return match ($this->role) {
            UserRole::Pengawas->value => 'v2.antrian.index',
            UserRole::Pimpinan->value => 'v2.dashboard',
            default => 'home',
        };
    }

    /**
     * Find user by email or phone number
     */
    public static function findByEmailOrPhone($identifier)
    {
        // Check if identifier is email format
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return static::where('email', $identifier)->first();
        }
        
        // Check if identifier is phone number format (basic check)
        if (preg_match('/^[0-9+\-\s()]+$/', $identifier)) {
            return static::where('nomor_hp', $identifier)->first();
        }
        
        return null;
    }

    /**
     * Get display name for user
     */
    public function getDisplayName()
    {
        return $this->nama ?: $this->email;
    }
}
