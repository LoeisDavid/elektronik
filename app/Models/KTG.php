<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('ktg', key: 'KTG', keyType: 'string', incrementing: false)]
#[Fillable(['KTG', 'DES'])]
class KTG extends Model
{
    function itm()
    {
        return ITM::where('KTG', $this->KTG)->get();
    }
    
    use HasFactory;
}
