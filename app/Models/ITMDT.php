<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('ITMDT', key: 'BC_OUT', keyType: 'string', incrementing: false)]
#[Fillable(['BC_OUT', 'ITM', 'BC_IN', 'TM', 'HGB', 'GD'])]
class ITMDT extends Model
{
    
}
