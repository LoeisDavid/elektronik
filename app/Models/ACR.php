<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('acr', key: 'REF_NO', keyType: 'string', incrementing: false)]
#[Fillable(['REF_NO', 'DT', 'CST', 'SLS', 'BRT','PAY','TOT_POT', 'JNS', 'POT_1', 'POT_2', 'POT_R', 'USER'])]
class ACR extends Model
{
    function cst()
    {
        return CST::where('CST', $this->CST)->first();
    }

    function sls()
    {
        return SLS::where('SLS', $this->SLS)->first();
    }

    function sts()
    {
        return STS::where('REF_NO', $this->REF_NO)->get();
    }

    function user()
    {
        return USER::where('USERNAME', $this->USER)->first();
    }

    use HasFactory;
}
