<?php

namespace App\Exports;

use App\Models\Laporan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LaporanExport implements FromQuery, WithMapping, ShouldAutoSize, WithColumnFormatting, WithHeadings
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
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

    public function map($laporan): array
    {
        return [
            $laporan->created_at ? \Carbon\Carbon::parse($laporan->created_at)->format('d-m-Y') : '-',
            $laporan->nomor_tiket,
            $laporan->nama_lengkap,
            "'".$laporan->nik,
            "'".$laporan->nomor_pengadu,
            $laporan->email,
            $laporan->jenis_kelamin,
            $laporan->alamat_lengkap,
            $laporan->tanggal_kejadian ? \Carbon\Carbon::parse($laporan->tanggal_kejadian)->format('d-m-Y') : '-',
            $laporan->lokasi,
            $laporan->judul,
            $laporan->detail,
            $laporan->dokumen_pendukung
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->getStyle('C')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        $event->sheet->getStyle('D')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT, // Format kolom C (NIK) sebagai teks
            'D' => NumberFormat::FORMAT_TEXT, // Format kolom C (NIK) sebagai teks
        ];
    }
}
