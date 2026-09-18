<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('sls', key: 'SLS', keyType: 'string', incrementing: false)]
#[Fillable(['SLS', 'DES', 'HP', 'AL'])]
class SLS extends Model
{
    use HasFactory;
}
