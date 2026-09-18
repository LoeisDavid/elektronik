<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;


#[Table(key: 'SLS', keyType: 'string', incrementing: false)]
#[Fillable(['SLS', 'DES', 'HP', 'AL'])]
class SLS extends Model
{
    
}
