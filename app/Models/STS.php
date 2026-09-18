<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('sts', key: 'REC', keyType: 'string', incrementing: false)]
#[Fillable(['REC', 'REF_NO', 'BC_OUT', 'ITM', 'HGJ', 'ST', 'POT_1', 'POT_2', 'POT_R', 'PPN', 'BRT'])]
class STS extends Model
{
    use HasFactory;
}
