<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('mrk', key: 'MRK', keyType: 'string', incrementing: false)]
#[Fillable(['MRK', 'DES'])]
class MRK extends Model
{
    function itm()
    {
        return ITM::where('MRK', $this->MRK)->get();
    }
    
    use HasFactory;
}
