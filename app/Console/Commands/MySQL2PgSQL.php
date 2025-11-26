<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MySQL2PgSQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:MySQL2PgSQL {tableName} {startFrom} {endTo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import MySQL to PostgreSQL. Example usage: php artisan app:MySQL2PgSQL dis_logs "2021-01-01 00:00:00" "2021-01-01 00:00:00"';

    /**
     * Execute the console command.
     * user for Migration on PT. Freeport Indonesia
     */
    public function handle()
    {
        $this->info('Import MySQL to PostgreSQL');
        $tableName = $this->argument('tableName'); //Table Name Import
        $startFrom = $this->argument('startFrom');
        $endFrom = $this->argument('endTo');

        if(Carbon::parse($startFrom)->format("Y_m") != Carbon::parse($endFrom)->format("Y_m")){
            $this->error('Start and End date must be in the same month');
            return;
        }

        // DB Connection
        $mysql = DB::connection('mysql');
        $pgsql = DB::connection('pgsql');

        try{
            switch ($tableName) {
                case 'dis_logs':
                    $DasLog = $mysql->table('das_logs');
                    // Create Table
                    $Ym = Carbon::parse($startFrom)->format("Y_m");
                    $table = "dis_logs_{$Ym}"; // New Name in PGSQL
                    if(!Schema::hasTable($table)){
                        $pgsql->statement("CREATE TABLE dis_logs_{$Ym} as SELECT * FROM dis_logs with no data");
                    }
                    $DisLog = $pgsql->table("dis_logs_{$Ym}");
                    $dis_logs = $DasLog->whereRaw("time_group >= '{$startFrom}' AND time_group <= '{$endFrom}'")->get();
                    foreach ($dis_logs as $log) {
                        $data = [
                            'parameter_id' => $log->parameter_id,
                            'data_status_id' => $log->data_status_id,
                            'value' => $log->value,
                            'value_correction' => $log->value_correction,
                            'is_averaged' => $log->is_averaged,
                            'is_sent_sispek' => $log->is_sent_sispek,
                            'sent_sispek_at' => $log->sent_sispek_at,
                            'time_group' => $log->time_group,
                            'measured_at' => $log->measured_at,
                        ];
                        $DisLog->updateOrInsert([
                            "parameter_id" => $data['parameter_id'],
                            "time_group" => $data['time_group'],
                        ],$data);
                    }
                    break;
                
                case 'measurements':
                    $MeasurementOld = $mysql->table('measurements');
                    // Create Table
                    $Ym = Carbon::parse($startFrom)->format("Y_m");
                    $table = "measurements_{$Ym}"; // New Name in PGSQL
                    if(!Schema::hasTable($table)){
                        $pgsql->statement("CREATE TABLE measurements_{$Ym} as SELECT * FROM measurements with no data");
                    }
                    $MeasurementNew = $pgsql->table("measurements_{$Ym}");
                    $measurements = $MeasurementOld->whereRaw("time_group >= '{$startFrom}' AND time_group <= '{$endFrom}'")->get();
                    foreach ($measurements as $log) {
                        $data = [
                            'parameter_id' => $log->parameter_id,
                            'data_status_id' => $log->data_status_id,
                            'value' => $log->value,
                            'value_correction' => $log->value_correction,
                            'time_group' => $log->time_group,
                        ];
                        $MeasurementNew->updateOrInsert([
                            "parameter_id" => $data['parameter_id'],
                            "time_group" => $data['time_group'],
                        ],$data);
                    }
                    break;
                case 'measurement_logs':
                    $MeasurementLogOld = $mysql->table('measurement_logs');
                    // Create Table
                    $Ym = Carbon::parse($startFrom)->format("Y_m");
                    $table = "measurement_logs_{$Ym}"; // New Name in PGSQL
                    if(!Schema::hasTable($table)){
                        $pgsql->statement("CREATE TABLE measurement_logs_{$Ym} as (SELECT * FROM measurement_logs)");
                    }
                    $MeasurementLogNew = $pgsql->table("measurement_logs_{$Ym}");
                    $measurement_logs = $MeasurementLogOld->whereRaw("time_group >= '{$startFrom}' AND time_group <= '{$endFrom}'")->get();
                    foreach ($measurement_logs as $log) {
                        $data = [
                            'parameter_id' => $log->parameter_id,
                            'unit_id' => $log->unit_id,
                            'is_averaged' => $log->is_averaged,
                            'value' => $log->value,
                            'xtimestamp' => $log->xtimestamp,
                        ];
                        $MeasurementLogNew->updateOrInsert([
                            "parameter_id" => $data['parameter_id'],
                            "xtimestamp" => $data['xtimestamp'],
                        ],$data);
                    }
                    break;
                
                default:
                    $this->warn("Invalid table name : {$tableName}");
                    break;
            }
        }catch(Exception $e){
            $this->error($e->getMessage());
            Log::error("[$tableName - Import Error] : ".$e->getMessage());
        }
    }
    function splitTimeByHour($startDateTime, $endDateTime) {
        $hoursArray = array();    
        // Konversi string ke objek Carbon
        $start = Carbon::parse($startDateTime);
        $end = Carbon::parse($endDateTime);
    
        // Perulangan untuk setiap jam dalam rentang waktu
        while ($start <= $end) {
            $hoursArray[] = $start->format('Y-m-d H:i:s');
            $start->addHour(); // Tambah 1 jam
        }
    
        return $hoursArray;
    }
}
