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

class LaporanExport implements WithMapping, ShouldAutoSize, WithColumnFormatting, WithHeadings, FromView
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
        $event->sheet->getStyle('D')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        $event->sheet->getStyle('E')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT, // Format kolom C (NIK) sebagai teks
            'E' => NumberFormat::FORMAT_TEXT, // Format kolom C (NIK) sebagai teks
        ];
    }
}
