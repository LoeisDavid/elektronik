<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('itm', key: 'ITM', keyType: 'string', incrementing: false)]
#[Fillable(['ITM', 'STOK', 'HRG', 'DES', 'KTG', 'MRK'])]
class ITM extends Model
{
    function itmdt()
    {
        return ITMDT::where('ITM', $this->ITM)->get();
    }

    function mrk()
    {
        return MRK::where('MRK', $this->MRK)->first();
    }

    function ktg()
    {
        return KTG::where('KTG', $this->KTG)->first();
    }
    
    use HasFactory;
}
