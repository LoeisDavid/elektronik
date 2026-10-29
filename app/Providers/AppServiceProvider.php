<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $perusahaan = (object) [
            'nama_perusahaan' => config('perusahaan.nama_perusahaan', 'Pomelo.'),
            'kota'            => config('perusahaan.kota', 'Sumenep'),
            'alamat'          => config('perusahaan.alamat', ''),
            'no_telp'         => config('perusahaan.no_telp', ''),
            'nama'            => config('perusahaan.nama_perusahaan', 'Pomelo.'),
            'telepon'         => config('perusahaan.no_telp', ''),
        ];

        View::share('perusahaan', $perusahaan);
        View::share('namaPerusahaan', $perusahaan->nama_perusahaan);
        View::share('kotaPerusahaan', $perusahaan->kota);
        View::share('alamatPerusahaan', $perusahaan->alamat);
        View::share('noTelpPerusahaan', $perusahaan->no_telp);
    }
}
