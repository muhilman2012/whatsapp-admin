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
            $file = $request->file('file');
            $data = Excel::toArray([], $file);

            // Validasi jika data kosong
            if (empty($data[0])) {
                return redirect()->back()->with('error', 'File Excel kosong atau format tidak sesuai.');
            }

            $rows = $data[0];
            $successCount = 0;
            $errorCount = 0;
            $errorDetails = [];

            foreach ($rows as $index => $row) {
                // Lewati baris kosong
                if (empty($row[0])) {
                    continue;
                }

                try {
                    // Validasi tanggal dan waktu
                    $date = $row[1] ?? null; // Kolom tanggal
                    $time = $row[2] ?? null; // Kolom waktu

                    // Konversi tanggal dari format serial atau teks
                    if (is_numeric($date)) {
                        // Jika tanggal berupa angka (serial Excel)
                        $date = Carbon::parse(Date::excelToDateTimeObject($date))->format('Y-m-d');
                    } elseif (!Carbon::hasFormat($date, 'd-m-Y')) {
                        // Jika format teks tidak sesuai
                        throw new \Exception("Format tanggal tidak valid di baris ke-" . ($index + 1));
                    } else {
                        // Jika format teks valid
                        $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
                    }

                    // Validasi dan konversi waktu
                    if (is_numeric($time)) {
                        // Jika waktu berupa angka (serial Excel)
                        $time = Carbon::parse(Date::excelToDateTimeObject($time))->format('H:i:s');
                    } elseif (Carbon::hasFormat($time, 'H:i') || Carbon::hasFormat($time, 'H:i:s')) {
                        // Jika waktu berupa teks valid
                        $time = Carbon::parse($time)->format('H:i:s');
                    } else {
                        throw new \Exception("Format waktu tidak valid di baris ke-" . ($index + 1));
                    }
                    $dateTime = "$date $time"; // Gabungkan tanggal dan waktu

                    // Simpan data ke database
                    DB::table('laporans')->insert([
                        'nomor_tiket' => $row[0],
                        'created_at' => $dateTime,
                        'nama_lengkap' => $row[3] ?? null,
                        'nik' => $row[4] ?? null,
                        'nomor_pengadu' => $row[5] ?? null,
                        'email' => $row[6] ?? null,
                        'jenis_kelamin' => $row[7] ?? null,
                        'alamat_lengkap' => $row[8] ?? null,
                        'tanggal_kejadian' => isset($row[9]) ? Carbon::createFromFormat('d-m-Y', $row[9])->format('Y-m-d') : null,
                        'lokasi' => $row[10] ?? null,
                        'judul' => $row[11] ?? null,
                        'detail' => $row[12] ?? null,
                        'kategori' => $row[13] ?? null,
                        'status' => "Proses verifikasi dan telaah",
                        'tanggapan' => "Laporan pengaduan Saudara dalam proses verifikasi & penelaahan.",
                        'sumber_pengaduan' => "tatap muka",
                        'disposisi' => $row[17] ?? null,
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errorDetails[] = [
                        'nomor_tiket' => $row[0] ?? 'Tidak ada',
                        'error' => $e->getMessage(),
                        'baris' => $index + 1,
                    ];

                    // Log error ke file log Laravel
                    \Log::error("Error di baris ke-" . ($index + 1) . ": " . $e->getMessage());
                }
            }

            $message = "Data berhasil diimport: $successCount. ";
            if ($errorCount > 0) {
                $message .= "Gagal: $errorCount. Detail error: ";
                foreach ($errorDetails as $detail) {
                    $message .= "[Nomor Tiket: " . $detail['nomor_tiket'] . ", Baris: " . $detail['baris'] . ", Error: " . $detail['error'] . "]; ";
                }
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}
