<?php

namespace App\Jobs;

use App\Models\Laporan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ExportPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $fileName;

    /**
     * Create a new job instance.
     *
     * @param $data
     * @param $fileName
     */
    public function __construct($data, $fileName)
    {
        $this->data = $data;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Create the PDF from the data
        $pdf = Pdf::loadView('admin.laporan.export.pdf', [
            'laporans' => $this->data,
            'tanggal' => now()->format('d-m-Y'),
            'jumlahPengaduan' => $this->data->count(),
        ])->setPaper('a4', 'landscape');

        // Save the PDF file to storage
        Storage::put('public/exports/' . $this->fileName, $pdf->output());
    }
}
