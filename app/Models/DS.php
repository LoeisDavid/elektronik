<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;

#[Table('cst', key: 'CST', keyType: 'string', incrementing: false)]
#[Fillable(['CST', 'DES', 'AL', 'TLP', 'DS'])]
class DS extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope('desa', function (Builder $builder) {
            $builder->whereNull('DS');
        });
    }

    public function getDsAttribute()
    {
        return $this->attributes['CST'] ?? null;
    }
}
