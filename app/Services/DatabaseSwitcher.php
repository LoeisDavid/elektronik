<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DatabaseSwitcher
{
    /**
     * Dapatkan daftar database yang dapat dipilih.
     *
     * @return array<int, string>
     */
    public static function getDatabases(): array
    {
        $databases = config('database.databases', []);

        if (empty($databases)) {
            $default = config('database.connections.mysql.database', 'elektronik');
            return [$default];
        }

        return array_values(array_unique(array_filter($databases)));
    }

    /**
     * Periksa apakah nama database termasuk dalam daftar yang diizinkan.
     */
    public static function isValid(string $database): bool
    {
        return in_array($database, self::getDatabases(), true);
    }

    /**
     * Dapatkan database aktif saat ini.
     */
    public static function getActiveDatabase(): string
    {
        $connection = config('database.default', 'mysql');
        return Config::get("database.connections.{$connection}.database") 
            ?? session('selected_database') 
            ?? config('database.connections.mysql.database', 'elektronik');
    }

    /**
     * Ganti koneksi database default ke database yang dipilih.
     */
    public static function switch(string $database): void
    {
        $connection = config('database.default', 'mysql');

        if (Config::get("database.connections.{$connection}.database") !== $database) {
            Config::set("database.connections.{$connection}.database", $database);
            DB::purge($connection);
        }
    }

    /**
     * Uji koneksi ke database yang dipilih.
     * Melempar exception jika database tidak ada atau tidak dapat terhubung.
     *
     * @throws \Throwable
     */
    public static function testConnection(string $database): void
    {
        $connection = config('database.default', 'mysql');
        
        // Simpan konfigurasi lama
        $previousDb = Config::get("database.connections.{$connection}.database");

        if ($previousDb === $database) {
            DB::connection($connection)->getPdo();
            return;
        }

        try {
            Config::set("database.connections.{$connection}.database", $database);
            DB::purge($connection);
            DB::reconnect($connection);
            DB::connection($connection)->getPdo();
        } catch (\Throwable $e) {
            // Kembalikan ke konfigurasi sebelumnya jika gagal
            if ($previousDb) {
                Config::set("database.connections.{$connection}.database", $previousDb);
                DB::purge($connection);
            }
            throw $e;
        }
    }
}
