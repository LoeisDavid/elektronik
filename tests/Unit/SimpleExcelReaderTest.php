<?php

namespace Tests\Unit;

use App\Services\SimpleExcelReader;
use PHPUnit\Framework\TestCase;

class SimpleExcelReaderTest extends TestCase
{
    public function test_parse_number_indonesian_formats()
    {
        $this->assertEquals(1.0, SimpleExcelReader::parseNumber('1,00'));
        $this->assertEquals(2.0, SimpleExcelReader::parseNumber('2,00'));
        $this->assertEquals(2700000.0, SimpleExcelReader::parseNumber('2.700.000,00'));
        $this->assertEquals(3000000.0, SimpleExcelReader::parseNumber('3.000.000'));
        $this->assertEquals(4250000.0, SimpleExcelReader::parseNumber('Rp 4.250.000,00'));
        $this->assertEquals(0.0, SimpleExcelReader::parseNumber('0'));
        $this->assertEquals(0.0, SimpleExcelReader::parseNumber(''));
    }

    public function test_col_letters_to_index()
    {
        $this->assertEquals(0, SimpleExcelReader::colLettersToIndex('A'));
        $this->assertEquals(1, SimpleExcelReader::colLettersToIndex('B'));
        $this->assertEquals(4, SimpleExcelReader::colLettersToIndex('E'));
        $this->assertEquals(5, SimpleExcelReader::colLettersToIndex('F'));
        $this->assertEquals(26, SimpleExcelReader::colLettersToIndex('AA'));
    }

    public function test_detect_columns_from_headers()
    {
        $headers = [
            'Kd. Barang',
            'Nama Barang',
            'Kd. Suppl',
            'Nama Suppl',
            'Saldo',
            'Hrg. B. Trk.',
            'Tgl. B. Trk.',
            'Total'
        ];

        $mapping = SimpleExcelReader::detectColumns($headers);
        $this->assertEquals(0, $mapping['kd_barang']);
        $this->assertEquals(1, $mapping['nama_barang']);
        $this->assertEquals(4, $mapping['saldo']);
        $this->assertEquals(5, $mapping['harga_beli']);
    }
}

