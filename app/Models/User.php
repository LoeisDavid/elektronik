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

    public const ROLE_ADMIN = 1;
    public const ROLE_KASIR = 2;
    public const ROLE_STORAGE = 3;
    public const ROLE_MANAGER = 4;

    protected $primaryKey = 'USERNAME';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getRoleNameAttribute(): string
    {
        return match ((int) $this->ROLE) {
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_ADMIN   => 'Admin',
            self::ROLE_STORAGE => 'Storage',
            self::ROLE_KASIR   => 'Kasir',
            default            => 'User',
        };
    }

    public function isManager(): bool
    {
        return (int) $this->ROLE === self::ROLE_MANAGER;
    }

    public function isAdmin(): bool
    {
        return (int) $this->ROLE === self::ROLE_ADMIN;
    }

    public function isStorage(): bool
    {
        return (int) $this->ROLE === self::ROLE_STORAGE;
    }

    public function isKasir(): bool
    {
        return (int) $this->ROLE === self::ROLE_KASIR;
    }

    public function isManagerOrAdmin(): bool
    {
        return $this->isManager() || $this->isAdmin();
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
