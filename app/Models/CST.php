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
        return CST::where('CST', $this->DS)->get();
    }

    function desa()
    {
        return $this->DS ? CST::where('CST', $this->DS)->first() : null;
    }

    public function isDesa(): bool
    {
        return empty($this->DS);
    }

    public function isCustomer(): bool
    {
        return !empty($this->DS);
    }

    public function scopeDesa($query)
    {
        return $query->whereNull('DS');
    }

    public function scopeCustomer($query)
    {
        return $query->whereNotNull('DS');
    }

    use HasFactory;
}
