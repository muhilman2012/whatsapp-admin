<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Laporan;

class UpdateStatusMassal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status:update-massal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memperbarui status massal dari "Tidak dapat diproses lebih lanjut" menjadi "Belum dapat diproses lebih lanjut".';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Update data massal
        $updatedRows = Laporan::where('status', 'Tidak dapat diproses lebih lanjut')
            ->update(['status' => 'Belum dapat diproses lebih lanjut']);

        // Informasi ke terminal
        $this->info("Total data yang diperbarui: {$updatedRows}");

        return 0;
    }
}
