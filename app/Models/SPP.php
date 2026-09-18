<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;


#[Table(key: 'SPP', keyType: 'string', incrementing: false)]
#[Fillable(['SPP', 'DES', 'TLP'])]
class SPP extends Model
{
    //
}
