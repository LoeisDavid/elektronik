<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('itmdt', key: 'BC_OUT', keyType: 'string', incrementing: false)]
#[Fillable(['BC_OUT', 'ITM', 'BC_IN', 'TM', 'HRB', 'GD'])]
class ITMDT extends Model
{
    function itm()
    {
        return ITM::where('ITM', $this->ITM)->first();
    }

    function gd()
    {
        return GD::where('GD', $this->GD)->first();
    }
    
    use HasFactory;
}
