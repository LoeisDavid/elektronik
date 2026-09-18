<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;


#[Table(key: 'CST', keyType: 'string', incrementing: false)]
#[Fillable(['CST', 'DES', 'TLP'])]
class CST extends Model
{
    
}
