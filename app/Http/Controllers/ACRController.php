<?php

namespace App\Http\Controllers;

use App\Models\ACR;

class ACRController extends Controller
{
    public function index()
    {
        $data = ACR::orderBy('DT', 'desc')
    ->orderBy('REF_NO', 'desc')
    ->limit(10)
    ->get();

    foreach ($data as $acr) {
        $detail = [];

        foreach ($acr->sts() as $item) {
            if (!isset($detail[$item->ITM])) {
                $detail[$item->ITM] = [
                    'nama' => $item->itm()->DES,
                    'qty' => 1,
                ];
            } else {
                $detail[$item->ITM]['qty']++;
            }
        }

        $acr->detail = array_values($detail);
    }
    

    return view('POS.transaksi', compact('data'));
    }
}