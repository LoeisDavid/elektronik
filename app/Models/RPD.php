<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('rpd', key: 'RPD', keyType: 'string', incrementing: false)]
#[Fillable(['RPD', 'RP', 'BC_OUT'])]
class RPD extends Model
{
    use HasFactory;

    protected $table = 'rpd';
    protected $primaryKey = 'RPD';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'RPD',
        'RP',
        'BC_OUT',
    ];

    public function rp()
    {
        return $this->belongsTo(RP::class, 'RP', 'RP');
    }

    public function itmdt()
    {
        return $this->belongsTo(ITMDT::class, 'BC_OUT', 'BC_OUT');
    }
}
