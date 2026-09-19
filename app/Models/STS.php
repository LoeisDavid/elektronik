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
    function acr()
    {
        return ACR::where('REF_NO', $this->REF_NO)->first();
    }

    function itmdt()
    {
        return ITMDT::where('BC_OUT', $this->BC_OUT)->first();
    }

    function itm()
    {
        return ITM::where('ITM', $this->ITM)->first();
    }
    use HasFactory;
}
