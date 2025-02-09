<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Institution;
use Illuminate\Support\Facades\Http;

class SyncInstitutions extends Command
{
    protected $signature = 'sync:institutions';
    protected $description = 'Sync institutions from external API to local database';

    public function handle()
    {
        $this->info('Starting to sync institutions from API...');
        $response = Http::withHeaders([
            'auth' => 'Bearer $2y$10$mRB59Z3R/4f8F3XLE4JTs.zEFfMWhvnXwRPVuaulBPEHpzohiZz2C',
            'token' => '{OWL6BJ0X-DFFJ-LE4P-X7AO-ZGLFWIDRPKXL}',
            'Content-Type' => 'application/json'
        ])->get('https://api-splp.layanan.go.id/lapor/3.0.0/masters/institutions/external?page_size=1000');

        $institutions = $response->json()['results']['data'] ?? [];

        foreach ($institutions as $institution) {
            Institution::updateOrCreate(
                ['id' => $institution['id']],
                ['name' => $institution['name']]
            );
        }

        $this->info('Sync completed successfully!');
    }
}
