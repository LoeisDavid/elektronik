<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table(key: 'USERNAME', keyType: 'string', incrementing: false)]
#[Fillable(['USERNAME', 'PASS', 'password', 'AL', 'HP', 'DES', 'ROLE'])]
#[Hidden(['password', 'PASS', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN      = 1;
    public const ROLE_KASIR      = 2;
    public const ROLE_STORAGE    = 3;
    public const ROLE_GUDANG     = 3;
    public const ROLE_MANAGER    = 4;
    public const ROLE_SUPERVISOR = 4;
    public const ROLE_IMPORT     = 5;
    public const ROLE_DEVELOPER  = 5;

    protected $primaryKey = 'USERNAME';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getRoleNameAttribute(): string
    {
        if ($this->isDeveloper()) {
            return 'Developer';
        }

        return match ((int) $this->ROLE) {
            self::ROLE_SUPERVISOR => 'Supervisor',
            self::ROLE_ADMIN      => 'Admin',
            self::ROLE_STORAGE    => 'Gudang',
            self::ROLE_KASIR      => 'Kasir',
            default               => 'User',
        };
    }

    /**
     * Level hierarki role (semakin besar semakin tinggi):
     * 4 = Developer, 3 = Supervisor, 2 = Admin, 1 = Gudang / Kasir
     */
    public function roleLevel(): int
    {
        if ($this->isDeveloper()) {
            return 4;
        }
        if ($this->isSupervisor()) {
            return 3;
        }
        if ($this->isAdminOnly()) {
            return 2;
        }
        return 1;
    }

    public function isDeveloper(): bool
    {
        return (int) $this->ROLE === self::ROLE_DEVELOPER
            || strtolower((string) $this->USERNAME) === 'adminhunt';
    }

    public function isSupervisor(): bool
    {
        return (int) $this->ROLE === self::ROLE_SUPERVISOR && !$this->isDeveloper();
    }

    public function isManager(): bool
    {
        return $this->isSupervisor() || $this->isDeveloper();
    }

    public function isAdminOnly(): bool
    {
        return (int) $this->ROLE === self::ROLE_ADMIN && !$this->isDeveloper();
    }

    public function isAdmin(): bool
    {
        return $this->isAdminOnly() || $this->isSupervisor() || $this->isDeveloper();
    }

    public function isStorage(): bool
    {
        return (int) $this->ROLE === self::ROLE_STORAGE;
    }

    public function isGudang(): bool
    {
        return $this->isStorage();
    }

    public function isKasir(): bool
    {
        return (int) $this->ROLE === self::ROLE_KASIR;
    }

    public function isImport(): bool
    {
        return $this->isDeveloper();
    }

    public function canExport(): bool
    {
        return $this->isAdminOnly() || $this->isSupervisor() || $this->isDeveloper();
    }

    public function canDelete(): bool
    {
        return $this->isSupervisor() || $this->isDeveloper();
    }

    public function canEditTransaksi(): bool
    {
        return $this->isSupervisor() || $this->isDeveloper();
    }

    public function canSeeMargin(): bool
    {
        return $this->isSupervisor() || $this->isDeveloper();
    }

    public function canManageUsers(): bool
    {
        return $this->isAdminOnly() || $this->isSupervisor() || $this->isDeveloper();
    }

    public function isManagerOrAdmin(): bool
    {
        return $this->canManageUsers();
    }

    /**
     * Daftar ID role yang boleh ditambahkan/diatur oleh user ini.
     */
    public function allowedRolesToAssign(): array
    {
        if ($this->isDeveloper()) {
            return [
                self::ROLE_DEVELOPER,
                self::ROLE_SUPERVISOR,
                self::ROLE_ADMIN,
                self::ROLE_STORAGE,
                self::ROLE_KASIR,
            ];
        }

        if ($this->isSupervisor()) {
            return [
                self::ROLE_SUPERVISOR,
                self::ROLE_ADMIN,
                self::ROLE_STORAGE,
                self::ROLE_KASIR,
            ];
        }

        if ($this->isAdminOnly()) {
            return [
                self::ROLE_ADMIN,
                self::ROLE_STORAGE,
                self::ROLE_KASIR,
            ];
        }

        return [];
    }

    /**
     * Apakah user ini boleh mengedit data / mengganti password dari $target?
     * Bisa ganti password sendiri dan user yang rolenya di bawah atau setara.
     */
    public function canEditUser(self $target): bool
    {
        if (!$this->canManageUsers()) {
            return false;
        }

        if ($this->USERNAME === $target->USERNAME) {
            return true;
        }

        return $this->roleLevel() >= $target->roleLevel();
    }

    /**
     * Hanya Supervisor dan Developer yang bisa menghapus user (tidak bisa hapus diri sendiri atau user di atasnya).
     */
    public function canDeleteUser(self $target): bool
    {
        if (!$this->canDelete()) {
            return false;
        }

        if ($this->USERNAME === $target->USERNAME) {
            return false;
        }

        if (strtolower((string) $target->USERNAME) === 'adminhunt') {
            return false;
        }

        return $this->roleLevel() >= $target->roleLevel();
    }

    public function getAuthPasswordName(): string
    {
        return 'PASS';
    }

    public function getPasswordAttribute(): ?string
    {
        return $this->attributes['PASS'] ?? null;
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['PASS'] = $value;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'PASS' => 'hashed',
        ];
    }
}
