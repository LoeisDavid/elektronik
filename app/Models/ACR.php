<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('ACR', key: 'REF_NO', keyType: 'string', incrementing: false)]
#[Fillable(['REF_NO', 'DT', 'CST', 'SLS', 'BRT', 'POT1', 'POT2', 'POTR', 'USER'])]
class ACR extends Model
{
    //
}
