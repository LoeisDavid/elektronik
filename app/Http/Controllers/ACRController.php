<?php

namespace App\Http\Controllers;

use App\Models\ACR;

class ACRController extends Controller
{
    public function index()
    {
        $transactions = ACR::orderBy('DT', 'desc')
            ->orderBy('REF_NO', 'desc')
            ->get()
            ->map(function ($acr) {
                return [
                    'kode' => $acr->REF_NO,
                    'tanggal' => $acr->DT
                        ? \Carbon\Carbon::parse($acr->DT)->format('d/m/Y')
                        : '-',
                    'waktu' => $acr->DT
                        ? \Carbon\Carbon::parse($acr->DT)->format('H:i')
                        : '-',
                    'total' => $acr->BRT ?? 0,
                    'metode' => '-',
                    'status' => 'Selesai',
                    'customer' => $acr->CST,
                    'sales' => $acr->SLS,
                    'pot1' => $acr->POT_1 ?? 0,
                    'pot2' => $acr->POT_2 ?? 0,
                    'potR' => $acr->POT_R ?? 0,
                    'user' => $acr->USER,
                    'items' => [],
                ];
            });

        return view('data.transaksi', compact('transactions'));
    }
}