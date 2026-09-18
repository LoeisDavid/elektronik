<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('STS', key: 'REC', keyType: 'string', incrementing: false)]
#[Fillable(['REC', 'REF_NO', 'BC_OUT', 'ITM', 'HGJ', 'ST', 'POT1', 'POT2', 'POTR', 'PPN', 'BRT'])]
class STS extends Model
{
    //
}
