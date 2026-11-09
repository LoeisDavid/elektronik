<?php

/**
 * ============================================================================
 * KONFIGURASI GLOBAL PROFIL PERUSAHAAN / TOKO
 * ============================================================================
 * Silakan ubah nilai variabel di bawah ini sesuai dengan identitas perusahaan.
 * Perubahan pada file ini akan otomatis diterapkan pada:
 * - Struk Pembayaran (Modal & Cetak PDF)
 * - Laporan Penjualan (Cetak Transaksi)
 * - Laporan Penjualan Per Desa
 * - Laporan Cek Stok (Opname Stok)
 * - Dokumen Perpindahan Barang (Surat Permintaan, Surat Jalan, Bukti Penerimaan)
 * ============================================================================
 */

$NAMA_PERUSAHAAN = 'Toko Bogor';
$KOTA            = 'Sumenep';
$ALAMAT          = 'Jl. Contoh Alamat No. 123';
$NO_TELP         = '0812-3456-7890';

return [
    'nama_perusahaan' => $NAMA_PERUSAHAAN,
    'kota'            => $KOTA,
    'alamat'          => $ALAMAT,
    'no_telp'         => $NO_TELP,

    // Alias pendukung
    'nama'            => $NAMA_PERUSAHAAN,
    'telepon'         => $NO_TELP,
];

