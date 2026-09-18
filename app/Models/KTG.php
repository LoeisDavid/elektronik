<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;


#[Table(key: 'KTG', keyType: 'string', incrementing: false)]
#[Fillable(['KTG', 'DES'])]
class KTG extends Model
{
    //
}
