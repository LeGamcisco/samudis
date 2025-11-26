<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackupTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup Table every midnight every month at 00:01';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tables = ['measurement_logs','dis_logs','measurements'];
        //$tables = ["dis_logs"];
        $lastMonth = now()->subMonth()->format("m_Y");
        if(now()->format("d H") != "01 00") return $this->error("Please run this command at 00:00 every day 1"); 
        foreach ($tables as $table) {
            try{
                $this->info("Backup [$table] ...");
                $newName = "{$table}_{$lastMonth}";
                $query = "CREATE TABLE {$newName} as (SELECT * FROM {$table})";
                $this->info($query);
                if(DB::statement($query)){
                    $this->info("TRUNCATE TABLE {$table} RESTART IDENTITY");
                    DB::statement("TRUNCATE TABLE {$table} RESTART IDENTITY");
                    // Select
                    if($table == "dis_logs"){
                        $now = now()->format("Y-m");
                        // Get existing data in current month
                        $currentData =  DB::table($newName)->whereRaw("to_char(time_group,'YYYY-MM') = '$now'")->get();
                        // Insert existing data to fresh table
                        foreach($currentData as $disLog){
                            DB::table($table)->insert([
                                'time_group' => $disLog->time_group,
                                'measured_at' => $disLog->measured_at,
                                'avg_time_group' => $disLog->avg_time_group,
                                'parameter_id' => $disLog->parameter_id,
                                'data_status_id' => $disLog->data_status_id,
                                'condition_id' => $disLog->condition_id,
                                'unit_id' =>$disLog->unit_id,
                                'value' => $disLog->value,
                                'value_correction' => $disLog->value_correction,
                                'is_sent_sispek' => 0,
                                'sent_sispek_at' => null,
                            ]);
                        }
                        
                    }
                }
            }catch(Exception $e){
                $this->error($e->getMessage());
                Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            }
        }
    }
}
