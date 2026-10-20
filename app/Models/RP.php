<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('rp', key: 'RP', keyType: 'string', incrementing: false)]
#[Fillable(['RP', 'DTRQ', 'DTCF1', 'DTCF2', 'GD_ASAL', 'GD_TUJUAN', 'SS', 'RQ', 'CF1', 'CF2'])]
class RP extends Model
{
    use HasFactory;

    protected $table = 'rp';
    protected $primaryKey = 'RP';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'RP',
        'DTRQ',
        'DTCF1',
        'DTCF2',
        'GD_ASAL',
        'GD_TUJUAN',
        'SS',
        'RQ',
        'CF1',
        'CF2',
    ];

    public function details()
    {
        return $this->hasMany(RPD::class, 'RP', 'RP');
    }

    public function rpd()
    {
        return $this->hasMany(RPD::class, 'RP', 'RP');
    }

    public function gudangAsal()
    {
        return $this->belongsTo(GD::class, 'GD_ASAL', 'GD');
    }

    public function gudangTujuan()
    {
        return $this->belongsTo(GD::class, 'GD_TUJUAN', 'GD');
    }

    public function userRequest()
    {
        return $this->belongsTo(User::class, 'RQ', 'USERNAME');
    }

    public function userConfirm1()
    {
        return $this->belongsTo(User::class, 'CF1', 'USERNAME');
    }

    public function userConfirm2()
    {
        return $this->belongsTo(User::class, 'CF2', 'USERNAME');
    }
}
