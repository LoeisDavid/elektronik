<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use RuntimeException;

abstract class Controller
{
function generateCode(string $des, string $table): string
{
    $des = strtoupper(trim($des));

    $words = preg_split(
        '/[^A-Z0-9]+/',
        $des,
        -1,
        PREG_SPLIT_NO_EMPTY
    );

    $letters = '';
    $numbers = [];

    foreach ($words as $word) {

        // Ambil nomor dari deskripsi/nama
        if (preg_match_all('/\d+/', $word, $matches)) {
            foreach ($matches[0] as $number) {
                $numbers[] = $number;
            }
        }

        // Ambil huruf saja
        $wordLetters = preg_replace('/[^A-Z]/', '', $word);

        if ($wordLetters === '') {
            continue;
        }

        // Hanya ambil 2 huruf pertama dari setiap kata
        $letters .= substr($wordLetters, 0, 2);
    }

    /*
     * Nomor dari deskripsi wajib dipertahankan.
     */
    $numberPart = implode('', $numbers);

    /*
     * Maksimal 10 karakter.
     *
     * Nomor diprioritaskan.
     * Huruf akan dipotong jika jumlahnya terlalu panjang.
     */
    if ($numberPart !== '') {
        $maxLetters = 10 - strlen($numberPart);

        if ($maxLetters < 0) {
            // Jika nomor lebih dari 10 karakter,
            // ambil 10 karakter pertama dari nomor.
            $code = substr($numberPart, 0, 10);
        } else {
            $code = substr($letters, 0, $maxLetters) . $numberPart;
        }
    } else {
        $code = substr($letters, 0, 10);
    }

    /*
     * Kalau masih kurang dari 10 karakter,
     * tambahkan angka.
     */
    if (strlen($code) < 10) {
        $code .= str_repeat('0', 10 - strlen($code));
    }

    // Pastikan tepat 10 karakter
    $code = substr($code, 0, 10);

    /*
     * Cek apakah kode sudah ada di database.
     */
    $exists = function (string $value) use ($table): bool {
        return DB::table($table)
            ->where($table, $value)
            ->exists();
    };

    // Kalau belum ada, gunakan kode tersebut
    if (!$exists($code)) {
        return $code;
    }

    /*
     * Jika kode sudah ada,
     * gunakan angka sebagai pembeda.
     *
     * Nomor seri dari deskripsi tetap dipertahankan
     * selama masih memungkinkan.
     */
    for ($i = 1; $i <= 999999999; $i++) {

        $suffix = (string) $i;

        // Sisakan tempat untuk angka pembeda
        $prefixLength = 10 - strlen($suffix);

        if ($prefixLength < 0) {
            continue;
        }

        $candidate = substr($code, 0, $prefixLength) . $suffix;

        if (!$exists($candidate)) {
            return $candidate;
        }
    }

    throw new RuntimeException('Tidak dapat membuat kode unik.');
}
}
