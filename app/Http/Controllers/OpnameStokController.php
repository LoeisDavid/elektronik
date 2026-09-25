<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\DB;
use PDO; // Wajib dipanggil

class OpnameStokController extends Controller
{
    public function index()
    {

// Benchmark::dd(function () {

// 1. Tarik data murni tanpa IF dari MySQL
$items = DB::select("
    SELECT 
        t1.BC_OUT as kode_barcode, 
        t2.DES as nama_barang, 
        t3.BC_OUT as status
    FROM itmdt t1
    LEFT JOIN itm t2 ON t1.ITM = t2.ITM 
    LEFT JOIN sts t3 ON t1.BC_OUT = t3.BC_OUT
");

// 2. Eksekusi IF di luar (PHP Level)
foreach ($items as $row) {
    // Jika status ada isinya = terjual, jika NULL = checking
    $row->status = $row->status ? 'sudah_terjual' : 'checking';
}
// });

return view('opname-stok', ['items' => $items]);
    }
}
