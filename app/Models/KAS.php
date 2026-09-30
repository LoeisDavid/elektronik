<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
#[Table('kas', key: 'TGL', keyType: 'date', incrementing: false)]
#[Fillable(['TGL', 'USER', 'AWLT', 'AWLNT', 'DBT', 'DBNT', 'CRT', 'CRNT', 'AKHT', 'AKHNT', 'TGLM', 'TGLK'])]
class KAS extends Model
{
    // Casting tipe data tanggal agar otomatis menjadi objek Carbon
    protected function casts(): array
    {
        return [
        'TGL'  => 'date',
        'tglm' => 'datetime',
        'tglk' => 'datetime',
    ];
    }

    function user(){
        return User::where('USERNAME', $this->USER)->first();
    }
}
