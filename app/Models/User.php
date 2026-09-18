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

    protected $primaryKey = 'USERNAME';
    public $incrementing = false;
    protected $keyType = 'string';

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
