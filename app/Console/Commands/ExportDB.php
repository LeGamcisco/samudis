<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class ExportDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:exportDB';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export DB .sql every midnight at 00:01';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            set_time_limit(0);
            $filepath = sprintf('backup/database/backup_%s.sql', "daily");
            $command = sprintf(
                'pg_dump --dbname=postgresql://%s:%s@localhost:5432/%s -c -f %s',
                config('database.connections.pgsql.username'),
                config('database.connections.pgsql.password'),
                config('database.connections.pgsql.database'),
                public_path($filepath)
            );
            $process = Process::command($command);
            $process->run();
            $this->info("Database has ben backup! at ".public_path($filepath));
        }catch(Exception $e){
            $this->error($e->getMessage());
            Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
        }
    }
}
