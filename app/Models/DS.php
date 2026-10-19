<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('ds', key: 'DS', keyType: 'string', incrementing: false)]
#[Fillable(['DS', 'DES'])]
class DS extends Model
{
    use HasFactory;
}
