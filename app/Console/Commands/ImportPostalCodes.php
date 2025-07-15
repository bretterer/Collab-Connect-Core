<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportPostalCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collabconnect:import-postal-codes
                            {--country=US : Country code to import (default: US)}
                            {--chunk=1000 : Number of records to process in each batch}
                            {--truncate : Truncate the table before importing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import postal codes from text file into the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $countryCode = strtoupper($this->option('country'));
        $chunkSize = (int) $this->option('chunk');
        $truncate = $this->option('truncate');

        $filePath = "zipcodes/{$countryCode}.txt";

        if (! Storage::exists($filePath)) {
            $this->error("File not found: storage/app/private/{$filePath}");

            return Command::FAILURE;
        }

        if ($truncate) {
            $this->info('Truncating postal_codes table...');
            DB::table('postal_codes')->truncate();
        }

        $this->info("Importing postal codes from {$filePath}...");

        $fileContent = Storage::get($filePath);
        $lines = explode("\n", $fileContent);
        $totalLines = count($lines);

        // Remove empty lines
        $lines = array_filter($lines, fn ($line) => ! empty(trim($line)));
        $totalRecords = count($lines);

        $this->info("Found {$totalRecords} records to import");

        $bar = $this->output->createProgressBar($totalRecords);
        $bar->start();

        $batch = [];
        $processed = 0;
        $errors = 0;

        foreach ($lines as $line) {
            $data = $this->parseLine($line);

            if ($data === null) {
                $errors++;
                $bar->advance();

                continue;
            }

            $batch[] = $data;
            $processed++;

            if (count($batch) >= $chunkSize) {
                $this->insertBatch($batch);
                $batch = [];
            }

            $bar->advance();
        }

        // Insert remaining records
        if (! empty($batch)) {
            $this->insertBatch($batch);
        }

        $bar->finish();
        $this->newLine();

        $this->info('Import completed!');
        $this->info("Total records processed: {$processed}");

        if ($errors > 0) {
            $this->warn("Records with errors: {$errors}");
        }

        return Command::SUCCESS;
    }

    /**
     * Parse a single line from the file.
     */
    private function parseLine(string $line): ?array
    {
        $fields = explode("\t", $line);

        // Ensure we have at least the minimum required fields
        if (count($fields) < 12) {
            return null;
        }

        try {
            return [
                'country_code' => $fields[0] ?? null,
                'postal_code' => $fields[1] ?? null,
                'place_name' => $fields[2] ?? null,
                'admin_name1' => ! empty($fields[3]) ? $fields[3] : null,
                'admin_code1' => ! empty($fields[4]) ? $fields[4] : null,
                'admin_name2' => ! empty($fields[5]) ? $fields[5] : null,
                'admin_code2' => ! empty($fields[6]) ? $fields[6] : null,
                'admin_name3' => ! empty($fields[7]) ? $fields[7] : null,
                'admin_code3' => ! empty($fields[8]) ? $fields[8] : null,
                'latitude' => ! empty($fields[9]) ? (float) $fields[9] : null,
                'longitude' => ! empty($fields[10]) ? (float) $fields[10] : null,
                'accuracy' => ! empty($fields[11]) ? (int) $fields[11] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Insert a batch of records into the database.
     */
    private function insertBatch(array $batch): void
    {
        try {
            DB::table('postal_codes')->insert($batch);
        } catch (\Exception $e) {
            $this->error('Error inserting batch: '.$e->getMessage());
        }
    }
}
