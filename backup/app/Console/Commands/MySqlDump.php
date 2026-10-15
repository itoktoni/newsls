<?php

namespace App\Console\Commands;

use Ifsnop\Mysqldump as IMysqldump;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MySqlDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the mysqldump utility using info from .env';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Backup Start');
        Log::info('Log Start');
        $name = date('Y-m-d-His').'_backup_database.gzip';

        try {

            $dumpSettingsDefault = [
                'compress' => 'Gzip',
                'single-transaction' => true,
                'exclude-tables' => [
                    'telescope_entries',
                    'telescope_entries_tags',
                    'telescope_monitoring',
                ],
            ];

            $dump = new IMysqldump\Mysqldump('mysql:host='.env('DB_HOST').';dbname='.env('DB_DATABASE').'', env('DB_USERNAME'), env('DB_PASSWORD'), $dumpSettingsDefault);
            $dump->start(storage_path('app/'.$name));

            $this->info('Backup Databaes '.$name);

            $contents = File::get(storage_path('app/'.$name));
            $alphara = Storage::disk('alphara')->put('Rebuild/'.$name, $contents);

            if ($alphara) {
                $this->info('Backup Finish '.$name);
            }

            unlink(storage_path('app/'.$name));

        } catch (\Exception $e) {
            $errror = 'mysqldump-php error: '.$e->getMessage();
            $this->error($errror);
        }
    }
}
