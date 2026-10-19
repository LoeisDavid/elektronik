<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('spp', key: 'SPP', keyType: 'string', incrementing: false)]
#[Fillable(['SPP', 'DES', 'TLP'])]
class SPP extends Model
{
    function itm()
    {
        return ITM::where('SPP', $this->SPP)->get();
    }

    function itmdt()
    {
        return ITMDT::where('SPP', $this->SPP)->get();
    }
    
    use HasFactory;
}
