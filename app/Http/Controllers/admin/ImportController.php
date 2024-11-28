<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date; // Import PhpSpreadsheet Date untuk konversi Excel serial

class ImportController extends Controller
{
    public function import(Request $request)
    {
        // Validasi file upload
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        try {
            // Membaca file Excel
            $file = $request->file('file');
            $data = Excel::toArray([], $file);

            // Validasi jika data kosong
            if (empty($data[0])) {
                return redirect()->back()->with('error', 'File Excel kosong atau format tidak sesuai.');
            }

            // Ambil data dari file
            $rows = $data[0];

            foreach ($rows as $index => $row) {
                // Lewati baris kosong
                if (empty($row[0])) {
                    continue;
                }

                // Validasi data penting
                if (empty($row[0])) { // Kolom pertama sebagai `nomor_tiket`
                    throw new \Exception("Data pada baris ke-" . ($index + 1) . " tidak memiliki 'nomor_tiket'.");
                }

                // Insert data ke database menggunakan query builder
                DB::table('laporans')->insert([
                    'nomor_tiket' => $row[0], // Kolom 0: nomor_tiket
                    'created_at' => isset($row[1]) && is_numeric($row[1]) ? 
                        Carbon::instance(Date::excelToDateTimeObject($row[1]))->format('Y-m-d H:i:s') : null, // Konversi serial Excel ke datetime
                    'nama_lengkap' => $row[2] ?? null, // Kolom 2: nama_lengkap
                    'nik' => isset($row[3]) ? ltrim($row[3], "'") : null, // Kolom 3: nik
                    'nomor_pengadu' => isset($row[4]) ? ltrim($row[4], "'") : null, // Kolom 4: nomor_pengadu
                    'email' => $row[5] ?? null, // Kolom 5: email
                    'jenis_kelamin' => $row[6] ?? null, // Kolom 6: jenis_kelamin
                    'alamat_lengkap' => $row[7] ?? null, // Kolom 7: alamat_lengkap
                    'tanggal_kejadian' => isset($row[8]) ? Carbon::createFromFormat('d-m-Y', $row[8])->format('Y-m-d') : null, // Kolom 8: tanggal_kejadian
                    'lokasi' => $row[9] ?? null, // Kolom 9: lokasi
                    'judul' => $row[10] ?? null, // Kolom 10: judul
                    'detail' => $row[11] ?? null, // Kolom 11: detail
                    'kategori' => $row[12] ?? null, // Kolom 12: kategori
                    'status' => $row[13] ?? null, // Kolom 13: status
                    'tanggapan' => "Laporan pengaduan Anda dalam proses verifikasi & penelaahan, sesuai ketentuan akan dilakukan dalam 14 (empat belas) hari kerja sejak laporan lengkap diterima.",
                    'sumber_pengaduan' => $row[14] ?? 'whatsapp', // Kolom 14: sumber_pengaduan
                ]);
            }

            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}
