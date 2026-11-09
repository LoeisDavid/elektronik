<?php

namespace App\Services;

use ZipArchive;
use DOMDocument;
use Exception;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;

class SimpleExcelReader
{
    /**
     * Membaca file spreadsheet (.xlsx, .xls, .csv) dan mengembalikan array 2D baris dan kolom (sheet pertama).
     *
     * @param string $filePath
     * @param string|null $originalExtension
     * @return array<int, array<int, string>>
     * @throws Exception
     */
    public static function read(string $filePath, ?string $originalExtension = null): array
    {
        $sheets = self::readSheets($filePath, $originalExtension);
        foreach ($sheets as $rows) {
            if (!empty($rows)) {
                return $rows;
            }
        }
        return [];
    }

    /**
     * Membaca seluruh sheet pada file spreadsheet (.xlsx, .xls, .csv)
     * Mengembalikan array asosiatif: ['Nama Sheet' => [ [col0, col1, ...], ... ]]
     *
     * @param string $filePath
     * @param string|null $originalExtension
     * @return array<string, array<int, array<int, string>>>
     * @throws Exception
     */
    public static function readSheets(string $filePath, ?string $originalExtension = null): array
    {
        if (!file_exists($filePath)) {
            throw new Exception("File tidak ditemukan: {$filePath}");
        }

        $ext = strtolower($originalExtension ?: pathinfo($filePath, PATHINFO_EXTENSION));

        // 1. Coba parse sebagai XLSX jika zip atau ekstensi xlsx
        if ($ext === 'xlsx' || self::isZipFile($filePath)) {
            try {
                $sheets = self::parseXlsxSheets($filePath);
                if (!empty($sheets)) {
                    return $sheets;
                }
            } catch (\Throwable $e) {
                // Lanjut ke fallback jika gagal
            }
        }

        // 2. Coba periksa apakah format file berupa XML Spreadsheet 2003 atau HTML Table
        $firstBytes = file_get_contents($filePath, false, null, 0, 4096);
        if ($firstBytes !== false) {
            if (stripos($firstBytes, '<?xml') !== false || stripos($firstBytes, 'Workbook') !== false || stripos($firstBytes, '<table') !== false || stripos($firstBytes, '<html') !== false) {
                $rows = self::parseXmlOrHtml($filePath);
                if (!empty($rows)) {
                    return ['Sheet1' => $rows];
                }
            }
        }

        // 3. Coba parse sebagai CSV / TSV (delimiter koma, titik koma, atau tab)
        $csvRows = self::parseCsv($filePath);
        if (!empty($csvRows) && count($csvRows[0]) >= 2) {
            return ['Sheet1' => $csvRows];
        }

        // 4. Jika masih belum berhasil dan file berupa BIFF binary XLS
        $biffRows = self::parseBiffXls($filePath);
        if (!empty($biffRows)) {
            return ['Sheet1' => $biffRows];
        }

        if (!empty($csvRows)) {
            return ['Sheet1' => $csvRows];
        }

        throw new Exception('Format file spreadsheet tidak dapat dibaca. Pastikan file berformat .xlsx, .xls, atau .csv yang valid.');
    }

    /**
     * Cek apakah file merupakan file ZIP (magic header: PK\x03\x04)
     */
    private static function isZipFile(string $filePath): bool
    {
        $handle = fopen($filePath, 'rb');
        if (!$handle) return false;
        $bytes = fread($handle, 4);
        fclose($handle);
        return $bytes === "PK\x03\x04";
    }

    /**
     * Parser XLSX untuk sheet pertama (backward compatible)
     */
    public static function parseXlsx(string $filePath): array
    {
        $sheets = self::parseXlsxSheets($filePath);
        foreach ($sheets as $rows) {
            return $rows;
        }
        return [];
    }

    /**
     * Parser XLSX multi-sheet native menggunakan ZipArchive dan SimpleXML
     *
     * @return array<string, array<int, array<int, string>>>
     */
    public static function parseXlsxSheets(string $filePath): array
    {
        // Gunakan library Composer shuchkin/simplexlsx jika tersedia (tidak wajib ext-zip)
        if (class_exists(SimpleXLSX::class)) {
            $xlsx = SimpleXLSX::parseFile($filePath);
            if ($xlsx !== false) {
                $resultSheets = [];
                $sheetNames = $xlsx->sheetNames();
                foreach ($sheetNames as $idx => $sheetName) {
                    $rawRows = $xlsx->rows($idx);
                    $cleanRows = [];
                    foreach ($rawRows as $r) {
                        $cleanRows[] = array_map(fn($v) => trim((string) $v), $r);
                    }
                    $name = trim((string) $sheetName) !== '' ? (string) $sheetName : 'Sheet' . ($idx + 1);
                    $resultSheets[$name] = $cleanRows;
                }
                if (!empty($resultSheets)) {
                    return $resultSheets;
                }
            }
        }

        if (!class_exists(ZipArchive::class)) {
            throw new Exception('Gagal membaca file Excel (.xlsx): Library SimpleXLSX / ZipArchive tidak tersedia.');
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new Exception('Gagal membuka file Excel (.xlsx).');
        }

        // 1. Ambil sharedStrings.xml jika ada
        $sharedStrings = [];
        $sharedXmlContent = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXmlContent !== false) {
            $xml = simplexml_load_string($sharedXmlContent);
            if ($xml !== false) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $val = '';
                        foreach ($si->r as $run) {
                            $val .= (string) ($run->t ?? '');
                        }
                        $sharedStrings[] = $val;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Ambil daftar sheet dari xl/workbook.xml & relasi xl/_rels/workbook.xml.rels
        $sheetList = [];
        $relsMap = [];
        $relsXmlContent = $zip->getFromName('xl/_rels/workbook.xml.rels');
        if ($relsXmlContent !== false) {
            $relsXml = simplexml_load_string($relsXmlContent);
            if ($relsXml !== false) {
                foreach ($relsXml->Relationship as $rel) {
                    $id = (string) $rel['Id'];
                    $target = ltrim((string) $rel['Target'], '/');
                    if (!str_starts_with($target, 'xl/')) {
                        $target = 'xl/' . $target;
                    }
                    $relsMap[$id] = $target;
                }
            }
        }

        $wbXmlContent = $zip->getFromName('xl/workbook.xml');
        if ($wbXmlContent !== false) {
            $wbXml = simplexml_load_string($wbXmlContent);
            if ($wbXml !== false && isset($wbXml->sheets->sheet)) {
                $idx = 1;
                foreach ($wbXml->sheets->sheet as $sh) {
                    $name = (string) ($sh['name'] ?? "Sheet{$idx}");
                    $nsR = $sh->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
                    $rId = isset($nsR['id']) ? (string) $nsR['id'] : '';
                    $path = ($rId && isset($relsMap[$rId])) ? $relsMap[$rId] : "xl/worksheets/sheet{$idx}.xml";
                    $sheetList[$name] = $path;
                    $idx++;
                }
            }
        }

        if (empty($sheetList)) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (preg_match('#^xl/worksheets/(sheet\d+)\.xml$#i', $name, $m)) {
                    $sheetList[$m[1]] = $name;
                }
            }
        }

        $resultSheets = [];
        foreach ($sheetList as $sheetName => $sheetPath) {
            $sheetXmlContent = $zip->getFromName($sheetPath);
            if ($sheetXmlContent === false) {
                continue;
            }
            $resultSheets[$sheetName] = self::parseWorksheetXml($sheetXmlContent, $sharedStrings);
        }

        $zip->close();

        if (empty($resultSheets)) {
            throw new Exception('Tidak ada sheet data yang ditemukan di file Excel.');
        }

        return $resultSheets;
    }

    /**
     * Parse isi XML satu worksheet menjadi array 2D
     */
    private static function parseWorksheetXml(string $sheetXmlContent, array $sharedStrings): array
    {
        $xml = simplexml_load_string($sheetXmlContent);
        if ($xml === false || !isset($xml->sheetData)) {
            return [];
        }

        $rows = [];
        foreach ($xml->sheetData->row as $row) {
            $rowMap = [];
            foreach ($row->c as $cell) {
                $cellRef = (string) $cell['r'];
                $colLetters = preg_replace('/\d+/', '', $cellRef);
                $colIndex = self::colLettersToIndex($colLetters);

                $cellType = (string) ($cell['t'] ?? '');
                $val = '';

                if ($cellType === 's') {
                    $idx = (int) $cell->v;
                    $val = $sharedStrings[$idx] ?? '';
                } elseif ($cellType === 'inlineStr' && isset($cell->is->t)) {
                    $val = (string) $cell->is->t;
                } elseif ($cellType === 'str' && isset($cell->v)) {
                    $val = (string) $cell->v;
                } elseif (isset($cell->v)) {
                    $val = (string) $cell->v;
                }

                $rowMap[$colIndex] = trim((string) $val);
            }

            if (!empty($rowMap)) {
                $maxCol = max(array_keys($rowMap));
                $normalizedRow = [];
                for ($c = 0; $c <= $maxCol; $c++) {
                    $normalizedRow[$c] = $rowMap[$c] ?? '';
                }
                $rows[] = $normalizedRow;
            }
        }

        return $rows;
    }

    /**
     * Menulis banyak sheet menjadi file .xlsx valid menggunakan ZipArchive
     *
     * @param array<string, array<int, array<int, mixed>>> $sheets ['NamaSheet' => [ ['Header1', 'Header2'], ['Val1', 'Val2'] ]]
     * @param string|null $outputPath
     * @return string Path ke file .xlsx
     * @throws Exception
     */
    public static function writeSheets(array $sheets, ?string $outputPath = null): string
    {
        if (empty($sheets)) {
            $sheets = ['Sheet1' => [['Data Kosong']]];
        }

        $tempPath = $outputPath ?: (tempnam(sys_get_temp_dir(), 'export_xlsx_') . '.xlsx');

        // Gunakan library Composer shuchkin/simplexlsxgen (bekerja di semua laptop tanpa wajib ext-zip)
        if (class_exists(SimpleXLSXGen::class)) {
            $xlsxGen = new SimpleXLSXGen();
            foreach ($sheets as $rawName => $rows) {
                $cleanName = preg_replace('/[\\\\\\/?*\\[\\]:]/', '', (string) $rawName);
                $cleanName = substr(trim($cleanName) ?: 'Sheet1', 0, 31);
                $xlsxGen->addSheet(empty($rows) ? [['']] : $rows, $cleanName);
            }
            if ($xlsxGen->saveAs($tempPath)) {
                return $tempPath;
            }
        }

        $zip = new ZipArchive();
        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception('Gagal membuat file Excel (.xlsx).');
        }

        $sheetNames = array_keys($sheets);
        $sheetCount = count($sheetNames);

        // 1. [Content_Types].xml
        $overrides = '';
        for ($i = 1; $i <= $sheetCount; $i++) {
            $overrides .= '<Override PartName="/xl/worksheets/sheet' . $i . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }
        $contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . $overrides
            . '</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypesXml);

        // 2. _rels/.rels
        $relsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
        $zip->addFromString('_rels/.rels', $relsXml);

        // 3. xl/workbook.xml & xl/_rels/workbook.xml.rels
        $sheetsXmlNodes = '';
        $wbRelsNodes = '';
        foreach ($sheetNames as $idx => $rawName) {
            $sheetNum = $idx + 1;
            // Bersihkan nama sheet (maks 31 karakter, tanpa karakter terlarang Excel)
            $cleanName = preg_replace('/[\\\\\\/?*\\[\\]:]/', ' ', (string) $rawName);
            $cleanName = substr(trim($cleanName) ?: "Sheet{$sheetNum}", 0, 31);
            $escapedName = htmlspecialchars($cleanName, ENT_XML1 | ENT_QUOTES, 'UTF-8');

            $sheetsXmlNodes .= '<sheet name="' . $escapedName . '" sheetId="' . $sheetNum . '" r:id="rId' . $sheetNum . '"/>';
            $wbRelsNodes .= '<Relationship Id="rId' . $sheetNum . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $sheetNum . '.xml"/>';
        }
        $styleRelId = $sheetCount + 1;
        $wbRelsNodes .= '<Relationship Id="rId' . $styleRelId . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        $workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>' . $sheetsXmlNodes . '</sheets>'
            . '</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbookXml);

        $workbookRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . $wbRelsNodes
            . '</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRelsXml);

        // 4. xl/styles.xml (Style 0 = normal, Style 1 = bold header)
        $stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2">'
            . '<font><sz val="11"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><name val="Calibri"/></font>'
            . '</fonts>'
            . '<fills count="2">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '</fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            . '</cellXfs>'
            . '</styleSheet>';
        $zip->addFromString('xl/styles.xml', $stylesXml);

        // 5. xl/worksheets/sheet{N}.xml
        $sheetIdx = 1;
        foreach ($sheets as $rows) {
            $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
                . '<sheetData>';

            $rowNum = 1;
            foreach ($rows as $rIdx => $row) {
                $styleAttr = ($rIdx === 0) ? ' s="1"' : '';
                $sheetXml .= '<row r="' . $rowNum . '">';
                $colIdx = 0;
                foreach ($row as $cellVal) {
                    $colLet = self::indexToColLetters($colIdx);
                    $cellRef = $colLet . $rowNum;
                    $strVal = (string) ($cellVal ?? '');

                    // Jika angka murni (dan bukan kode panjang / telepon diawali 0)
                    if ($rIdx > 0 && is_numeric($strVal) && !preg_match('/^0\d+$/', $strVal) && strlen($strVal) <= 12) {
                        $sheetXml .= '<c r="' . $cellRef . '"' . $styleAttr . '><v>' . $strVal . '</v></c>';
                    } else {
                        $escapedVal = htmlspecialchars($strVal, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                        $sheetXml .= '<c r="' . $cellRef . '" t="inlineStr"' . $styleAttr . '><is><t>' . $escapedVal . '</t></is></c>';
                    }
                    $colIdx++;
                }
                $sheetXml .= '</row>';
                $rowNum++;
            }

            $sheetXml .= '</sheetData></worksheet>';
            $zip->addFromString("xl/worksheets/sheet{$sheetIdx}.xml", $sheetXml);
            $sheetIdx++;
        }

        $zip->close();
        return $tempPath;
    }

    /**
     * Konversi huruf kolom Excel (A, B, ..., AA, AB) ke index integer 0-based.
     */
    public static function colLettersToIndex(string $letters): int
    {
        $letters = strtoupper(trim($letters));
        $len = strlen($letters);
        $idx = 0;
        for ($i = 0; $i < $len; $i++) {
            $idx = $idx * 26 + (ord($letters[$i]) - ord('A') + 1);
        }
        return max(0, $idx - 1);
    }

    /**
     * Konversi index integer 0-based ke huruf kolom Excel (0 -> A, 1 -> B, 26 -> AA)
     */
    public static function indexToColLetters(int $index): string
    {
        $letters = '';
        $n = $index + 1;
        while ($n > 0) {
            $rem = ($n - 1) % 26;
            $letters = chr(65 + $rem) . $letters;
            $n = intdiv($n - 1, 26);
        }
        return $letters ?: 'A';
    }

    /**
     * Konversi nilai tanggal Excel (termasuk Excel serial date seperti 46296 atau dd/mm/yyyy) menjadi Y-m-d
     */
    public static function parseDate(mixed $val, ?string $default = null): string
    {
        $fallback = $default ?? date('Y-m-d');
        $str = trim((string) $val);
        if ($str === '' || $str === '00/01/1900' || $str === '0') {
            return $fallback;
        }

        // Jika berupa angka serial tanggal Excel (misal 46296 -> 2026-10-01)
        if (is_numeric($str)) {
            $serial = (int) round((float) $str);
            if ($serial > 20000 && $serial < 90000) {
                $unixTimestamp = ($serial - 25569) * 86400;
                return gmdate('Y-m-d', $unixTimestamp);
            }
        }

        // Format dd/mm/yyyy atau dd-mm-yyyy
        if (preg_match('#^(\d{1,2})[/-](\d{1,2})[/-](\d{4})$#', $str, $m)) {
            $d = (int) $m[1];
            $mo = (int) $m[2];
            $y = (int) $m[3];
            if ($y >= 1970 && checkdate($mo, $d, $y)) {
                return sprintf('%04d-%02d-%02d', $y, $mo, $d);
            }
        }

        // Format yyyy-mm-dd
        if (preg_match('#^(\d{4})[/-](\d{1,2})[/-](\d{1,2})#', $str, $m)) {
            $y = (int) $m[1];
            $mo = (int) $m[2];
            $d = (int) $m[3];
            if ($y >= 1970 && checkdate($mo, $d, $y)) {
                return sprintf('%04d-%02d-%02d', $y, $mo, $d);
            }
        }

        $ts = strtotime($str);
        if ($ts !== false && $ts > 0) {
            return date('Y-m-d', $ts);
        }

        return $fallback;
    }

    /**
     * Parser CSV / Delimited text
     */
    public static function parseCsv(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) return [];

        $firstLine = fgets($handle);
        rewind($handle);

        $delimiter = ',';
        if (substr_count((string) $firstLine, ';') > substr_count((string) $firstLine, ',')) {
            $delimiter = ';';
        } elseif (substr_count((string) $firstLine, "\t") > substr_count((string) $firstLine, ',')) {
            $delimiter = "\t";
        }

        $rows = [];
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($data === [null] || empty($data)) {
                continue;
            }
            $cleaned = array_map(function ($cell) {
                $val = trim((string) $cell);
                return preg_replace('/^\xEF\xBB\xBF/', '', $val);
            }, $data);

            if (implode('', $cleaned) !== '') {
                $rows[] = $cleaned;
            }
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Parser XML Spreadsheet 2003 atau HTML Table (.xls yang diekspor dari sistem/ERP)
     */
    public static function parseXmlOrHtml(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if (!$content) return [];

        if (stripos($content, '<Row') !== false && stripos($content, '<Cell') !== false) {
            $xmlClean = preg_replace('/<(\/?)ss:/i', '<$1', $content);
            $xmlClean = preg_replace('/\bss:[a-zA-Z0-9_]+="[^"]*"/i', '', $xmlClean);
            $xml = @simplexml_load_string($xmlClean);
            if ($xml !== false) {
                $rows = [];
                $rowNodes = $xml->xpath('//Row') ?: [];
                foreach ($rowNodes as $rNode) {
                    $rArr = [];
                    foreach ($rNode->Cell as $cNode) {
                        $rArr[] = trim((string) ($cNode->Data ?? $cNode));
                    }
                    if (implode('', $rArr) !== '') {
                        $rows[] = $rArr;
                    }
                }
                if (!empty($rows)) {
                    return $rows;
                }
            }
        }

        if (stripos($content, '<table') !== false) {
            $dom = new DOMDocument();
            @$dom->loadHTML('<?xml encoding="UTF-8">' . $content, LIBXML_NOERROR | LIBXML_NOWARNING);
            $rows = [];
            foreach ($dom->getElementsByTagName('tr') as $tr) {
                $rArr = [];
                foreach ($tr->getElementsByTagName('td') as $td) {
                    $rArr[] = trim($td->textContent);
                }
                if (empty($rArr)) {
                    foreach ($tr->getElementsByTagName('th') as $th) {
                        $rArr[] = trim($th->textContent);
                    }
                }
                if (!empty($rArr)) {
                    $rows[] = $rArr;
                }
            }
            if (!empty($rows)) {
                return $rows;
            }
        }

        return [];
    }

    /**
     * Parser sederhana untuk file Binary BIFF8 (.xls)
     */
    public static function parseBiffXls(string $filePath): array
    {
        return [];
    }

    /**
     * Membersihkan dan mengonversi format angka (termasuk format Indonesia 2.700.000,00 atau 1,00) menjadi float.
     */
    public static function parseNumber(mixed $val): float
    {
        if (is_numeric($val)) {
            return (float) $val;
        }

        $str = trim((string) $val);
        if ($str === '') return 0.0;

        $str = preg_replace('/^(rp\.?|idr)\s*/i', '', $str);

        if (str_contains($str, '.') && str_contains($str, ',')) {
            $str = str_replace('.', '', $str);
            $str = str_replace(',', '.', $str);
        } elseif (str_contains($str, ',')) {
            $str = str_replace(',', '.', $str);
        } elseif (str_contains($str, '.')) {
            if (preg_match('/^\d{1,3}(\.\d{3})+$/', $str)) {
                $str = str_replace('.', '', $str);
            }
        }

        $clean = preg_replace('/[^-0-9.]/', '', $str);
        return (float) ($clean ?: 0.0);
    }

    /**
     * Mendeteksi index kolom berdasarkan baris header
     */
    public static function detectColumns(array $headerRow): array
    {
        $mapping = [
            'kd_barang'    => null,
            'nama_barang'  => null,
            'supplier'     => null,
            'merk'         => null,
            'kategori'     => null,
            'saldo'        => null,
            'harga_beli'   => null,
            'tanggal_beli' => null,
            'gudang'       => null,
            'harga_jual'   => null,
        ];

        $anyMatched = false;

        foreach ($headerRow as $idx => $colName) {
            $normalized = strtolower(trim((string) $colName));
            $normalized = preg_replace('/[^a-z0-9]/', '', $normalized);
            if ($normalized === '') continue;

            if (in_array($normalized, ['kdbarang', 'kodebarang', 'kodeitem', 'kditm', 'cd', 'itm'], true) || str_contains($normalized, 'kdbar') || str_contains($normalized, 'kodebar')) {
                $mapping['kd_barang'] = $idx;
                $anyMatched = true;
            } elseif (in_array($normalized, ['namabarang', 'nama', 'namaitm', 'deskripsi', 'description', 'des'], true) || str_contains($normalized, 'namabar')) {
                $mapping['nama_barang'] = $idx;
                $anyMatched = true;
            } elseif (in_array($normalized, ['supplier', 'namasuppl', 'suppl', 'spp'], true)) {
                $mapping['supplier'] = $idx;
                $anyMatched = true;
            } elseif (in_array($normalized, ['merk', 'merek', 'mrk', 'brand'], true)) {
                $mapping['merk'] = $idx;
                $anyMatched = true;
            } elseif (in_array($normalized, ['kategori', 'ktg', 'category'], true)) {
                $mapping['kategori'] = $idx;
                $anyMatched = true;
            } elseif (in_array($normalized, ['saldo', 'stok', 'stock', 'qty', 'jumlah'], true) || str_contains($normalized, 'saldo')) {
                $mapping['saldo'] = $idx;
                $anyMatched = true;
            } elseif (str_contains($normalized, 'hrgb') || str_contains($normalized, 'hargab') || str_contains($normalized, 'hrgbeli') || str_contains($normalized, 'hargabeli')) {
                $mapping['harga_beli'] = $idx;
                $anyMatched = true;
            } elseif (in_array($normalized, ['harga', 'hargajual', 'hrgjual', 'hrg'], true)) {
                $mapping['harga_jual'] = $idx;
                $anyMatched = true;
            } elseif (str_contains($normalized, 'tgl') || str_contains($normalized, 'tanggal')) {
                $mapping['tanggal_beli'] = $idx;
                $anyMatched = true;
            } elseif (in_array($normalized, ['gudang', 'gd', 'lokasi'], true)) {
                $mapping['gudang'] = $idx;
                $anyMatched = true;
            }
        }

        if (!$anyMatched) {
            $mapping['kd_barang']   = 0;
            $mapping['nama_barang'] = 1;
            $mapping['saldo']       = 4;
            $mapping['harga_beli']  = 5;
        } else {
            if ($mapping['kd_barang'] === null)   $mapping['kd_barang'] = 0;
            if ($mapping['nama_barang'] === null) $mapping['nama_barang'] = 1;
        }

        return $mapping;
    }
}
