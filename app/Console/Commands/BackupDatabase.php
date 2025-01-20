<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\DbDumper\Databases\MySql;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database secara otomatis dan hapus backup lama';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Konfigurasi database
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbHost = env('DB_HOST', '127.0.0.1');
        $backupPath = storage_path('app/backup');
        $retentionDays = 14; // Durasi maksimal backup (dalam hari)

        // Pastikan folder backup ada
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        // Nama file backup berdasarkan tanggal
        $fileName = 'backup-' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';

        // Proses backup database
        try {
            MySql::create()
                ->setDbName($dbName)
                ->setUserName($dbUser)
                ->setPassword($dbPass)
                ->setHost($dbHost)
                ->dumpToFile($backupPath . '/' . $fileName);

            $this->info('Backup berhasil dibuat: ' . $fileName);
        } catch (\Exception $e) {
            $this->error('Gagal membuat backup: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // Hapus backup lama
        $this->deleteOldBackups($backupPath, $retentionDays);

        return Command::SUCCESS;
    }

    /**
     * Hapus file backup yang lebih lama dari jumlah hari tertentu.
     */
    private function deleteOldBackups($backupPath, $retentionDays)
    {
        $files = glob($backupPath . '/*.sql'); // Ambil semua file .sql di folder backup
        $now = Carbon::now();

        foreach ($files as $file) {
            $fileModifiedTime = Carbon::createFromTimestamp(filemtime($file));

            // Jika file lebih lama dari $retentionDays, hapus file
            if ($fileModifiedTime->diffInDays($now) > $retentionDays) {
                unlink($file); // Hapus file
                $this->info('Backup lama dihapus: ' . basename($file));
            }
        }
    }
}
