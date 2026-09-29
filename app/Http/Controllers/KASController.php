<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KASController extends Controller
{
    public function index()
    {
        return view('POS.laporan');
    }
}
