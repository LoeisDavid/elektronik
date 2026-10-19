<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('cst', key: 'CST', keyType: 'string', incrementing: false)]
#[Fillable(['CST', 'DES', 'AL', 'TLP', 'DS'])]
class CST extends Model
{
    function acr()
    {
        return ACR::where('CST', $this->CST)->get();
    }

    function ds()
    {
        return DS::where('DS', $this->DS)->get();
    }

    use HasFactory;
}
