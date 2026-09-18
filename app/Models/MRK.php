<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table(key: 'MRK', keyType: 'string', incrementing: false)]
#[Fillable(['MRK', 'DES'])]
class MRK extends Model
{
    //
}
