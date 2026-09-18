<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('itm', key: 'ITM', keyType: 'string', incrementing: false)]
#[Fillable(['ITM', 'STOK', 'HRG', 'DES', 'KTG', 'MRK', 'GD', 'SPP'])]
class ITM extends Model
{
    use HasFactory;
}
