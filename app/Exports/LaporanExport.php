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
use Maatwebsite\Excel\Concerns\WithChunkReading;

class LaporanExport implements
    FromQuery,
    WithMapping,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithChunkReading,
    ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Tanggal Pengaduan',
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
            'Kategori',
            'Status',
            'Tanggapan',
            'Sumber Pengaduan'
        ];
    }

    public function map($row): array
    {
        return [
            optional($row->created_at)->format('d-m-Y'),
            $row->nomor_tiket,
            $row->nama_lengkap,
            "'" . $row->nik,
            "'" . $row->nomor_pengadu,
            $row->email,
            $row->jenis_kelamin,
            $row->alamat_lengkap,
            optional($row->tanggal_kejadian)->format('d-m-Y'),
            $row->lokasi,
            $row->judul,
            $row->detail,
            $row->kategori,
            $row->status,
            $row->tanggapan,
            $row->sumber_pengaduan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['argb' => '538DD5'],
            ],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Border seluruh data
        $sheet->getStyle('A1:P' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin', 'color' => ['argb' => '000000']],
            ],
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 25,
            'D' => 18,
            'E' => 18,
            'F' => 30,
            'G' => 10,
            'H' => 40,
            'I' => 20,
            'J' => 30,
            'K' => 35,
            'L' => 50,
            'M' => 20,
            'N' => 15,
            'O' => 50,
            'P' => 10,
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
