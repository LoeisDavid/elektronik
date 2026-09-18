<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table(key: 'GD', keyType: 'string', incrementing: false)]
#[Fillable(['GD', 'DES'])]
class GD extends Model
{
    //
}
