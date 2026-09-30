<?php

namespace App\Http\Controllers;

use App\Models\ACR;
use Illuminate\Http\Request;

class ACRController extends Controller
{
    public function index(Request $request)
    {
        $query = ACR::query();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where('REF_NO', 'LIKE', "%{$keyword}%")
                //   ->orWhere('BRT', 'LIKE', "%{$keyword}%")
                //   ->orWhere('DT', 'LIKE', "%{$keyword}%")
                ;
        }

        if ($request->sort == 'terbaru') {
            $query->orderBy('DT', 'desc');
        } else if($request->sort == 'terlama') {
            $query->orderBy('DT', 'asc');
        } else if($request->sort == 'termahal') {
            $query->orderBy('BRT', 'desc');
        } else if($request->sort == 'termurah') {
            $query->orderBy('BRT', 'asc');
        } else {
            $query->orderBy('DT', 'desc');
        }

        $data = $query->paginate(10)->withQueryString();

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