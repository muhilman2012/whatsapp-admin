<?php

namespace App\Exports;

use App\Models\Laporan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class LaporanExport implements FromView, WithStyles, WithColumnWidths
{
    protected $startDate;
    protected $endDate;
    protected $data;

    public function view(): View
    {
        return view('admin.laporan.export.laporan', [
            'laporans' => $this->data
        ]);
    }

    public function __construct($data)
    {
        $this->data = $data->map(function ($item) {
            $item->created_at = $item->created_at->format('d-m-Y');
            $item->nik = "'" . $item->nik; // Tambahkan kutipan tunggal di depan NIK
            $item->nomor_pengadu = "'" . $item->nomor_pengadu; // Tambahkan kutipan tunggal di depan Nomor Pengadu
            return $item;
        });
    }

    public function query()
    {
        return Laporan::query()
            ->whereBetween('created_at', [$this->startDate, $this->endDate]);
    }

    public function headings(): array
    {
        return [
            'Tgl Pengaduan',
            'Nomor Tiket',
            'Nama Lengkap',
            'NIK',
            'Nomor Pengadu',
            'Email',
            'Jenis Kelamin',
            'Alamat Lengkap',
            'Tanggal Kejadian',
            'Lokasi',
            'Judul',
            'Detail Pengaduan',
            'Dokumen Pendukung'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header tabel
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'], // Warna teks putih
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['argb' => '538DD5'], // Latar belakang biru
            ],
            'alignment' => [
                'horizontal' => 'center', // Pusatkan teks header
                'vertical' => 'center',
            ],
        ]);

        // Style border untuk semua tabel
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:O' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['argb' => '000000'], // Warna border hitam
                ],
            ],
        ]);

        // Tinggi baris default
        $sheet->getDefaultRowDimension()->setRowHeight(20);

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Nomor Tiket
            'B' => 20, // Tanggal Pengaduan
            'C' => 25, // Nama Lengkap
            'D' => 18, // NIK
            'E' => 18, // Nomor Pengadu
            'F' => 30, // Email
            'G' => 10, // Jenis Kelamin
            'H' => 40, // Alamat Lengkap
            'I' => 20, // Tanggal Kejadian
            'J' => 30, // Lokasi
            'K' => 35, // Judul
            'L' => 50, // Detail
            'M' => 20, // Kategori
            'N' => 15, // Status
            'O' => 50, // Tanggapan
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->getStyle('D')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        $event->sheet->getStyle('E')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
    }
}
