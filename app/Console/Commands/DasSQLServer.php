<?php

namespace App\Console\Commands;

use App\Models\MeasurementLog;
use App\Models\Parameter;
use DivisionByZeroError;
use Error;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DasSQLServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:das-sql-server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Running Data Acquisition System from SQL Server';

    /**
     * Execute the console command.
     * used for PT. Freeport Indonesia
     */
    public function handle()
    {
        $parameters = Parameter::selectRaw("id,parameter_id,unit_id,tag_id,status_id,formula")->get();
        foreach ($parameters as $parameter) {
            $tagId = $parameter->tag_id;
            $formula = $parameter->formula;
            $rawValue = $this->getValue($tagId);
            if($rawValue){
                $value = $rawValue["value"];
                $value_correction = $rawValue["value_correction"];
                $date = $rawValue["date"];
                try{
                    switch ($parameter->parameter_id) {
                        case '3': // Particulate Stack 1
                            $pmd1_id = $parameter->parameter_id;
                            $pmd1 = $value;
                            $pmd1_status = $parameter->status_id;
                            break;
                        case '4': // O2 Ducting 1
                            $o2d1 = $value;
                            $o2d1_status = $parameter->status_id;
                            break;
                        case '5': // Flow Stack 1.2
                            $flowstack12 = $value;
                            break;
                        case '8': // Particulate Stack 2
                            $pmd2_id = $parameter->parameter_id;
                            $pmd2 = $value;
                            $pmd2_status = $parameter->status_id;
                            break;
                        case '9': // O2 Ducting 2
                            $o2d2 = $value;
                            $o2d2_status = $parameter->status_id;
                            break;
                        case '10': // Flow Stack 1.2
                            $value = $value / 2;
                            $value_correction = $value_correction / 2;
                            break;
                        case '16': // HG Stack 1
                            $hgd1_id = $parameter->parameter_id;
                            $hgd1 = $value / 1000;
                            $value_correction = $value_correction / 1000;
                        case '18': // HG Stack 2
                            $hgd2_id = $parameter->parameter_id;
                            $hgd2 = $value / 1000;
                            $value_correction = $value_correction / 1000;
                            break;
                        case '20': // HG Stack 1.2
                            $value = $value / 1000;
                            $value_correction = $value_correction / 1000;
                            break;
                        case '24': // Flow Ducting 1
                            $flowd1 = $value;
                            break;
                        case '25': // Flow Ducting 2
                            $flowd2 = $value;
                            break;
                        default:
                            $value = $rawValue;
                            $value_correction = $rawValue;
                            break;
                    }
                    if(!in_array($parameter->parameter_id, [3,8,16,18,24,25])){
                        MeasurementLog::create([
                            "parameter_id" => $parameter->parameter_id,
                            "unit_id" => $parameter->unit_id,
                            "data_status_id" => $parameter->status_id,
                            "value" => $value, 
                            "corrective" => $value_correction,
                            "xtimestamp" => $date
                        ]);
                    }

                    $this->info("Applying formula: $formula = $value from $rawValue\n");
                }catch(Exception | Error | DivisionByZeroError $e){
                    $this->error("Error while processing formula: ".$e->getMessage());
                    Log::error("Error while processing formula: ".$e->getMessage());
                }
            }
        }

        if($o2d1_status != 1 && $o2d2_status != 1){
            $hgdunit_1 = '0.000001';
            $hgdunit_2 = '0.000001';
            $pmdunit_1 = '1';
            $pmdunit_2 = '1';
            foreach ([$pmd1_id, $pmd2_id] as $id) {
                MeasurementLog::create([
                    "parameter_id" => $id,
                    "value" => 1,
                    "corrective" => 1,
                    "xtimestamp" => date('Y-m-d H:i:s'),
                ]);
            }
            foreach ([$hgd1_id, $hgd2_id] as $id) {
                MeasurementLog::create([
                    "parameter_id" => $id,
                    "value" => 0.000001,
                    "corrective" => 0.000001,
                    "xtimestamp" => date('Y-m-d H:i:s'),
                ]);
            }
            return;
        }else if($o2d1_status != 1){
            // Menghitung Deviasi
            $deviasi = abs((0 + $flowd2) - $flowstack12);
            // Jika Ada Deviasi
            if($deviasi != 0){
                // Flow Ducting Unit 2 Calculation
                $fd2_deviasi = $flowd2 / ($flowd2 + 0) * $deviasi;
                // HG Ducting Unit 2 Calculation
                $hgdunit_1 = '0.000001';
                $hgdunit_2 = 1 / ($fd2_deviasi / (($fd2_deviasi + 0) / 2 )) * $hgd2;
                // PM Ducting Unit 2 Calculation
                $pmdunit_1 = '1';
                $pmdunit_2 = 1 / ($fd2_deviasi / (($fd2_deviasi + 0) / 2 )) * $pmd2;
            }else{
                $hgdunit_1 = '0.000001';
                $hgdunit_2 = 1 / ($flowd2 / (($flowd2 + 0) / 2 )) * $hgd2;
                
                // PM Ducting Unit 2 Calculation
                $pmdunit_1 = '1';
                $pmdunit_2 = 1 / ($flowd2 / (($flowd2 + 0) / 2 )) * $pmd2;
            }

        }
    }

    public function getValue($tagId){
        try{
            $odbc = $this->getODCConnection();
            $get = odbc_exec($odbc,"SELECT TOP 1 * FROM ReadingAverageData WHERE ReadingAverageDataTagID = '{$tagId}' ORDER BY Date DESC");
            $row = odbc_result($get,"FinalValue") ?? 0;
            $date = odbc_result($get,"Date");
            return ["value" => $row, "date" => $date];
        }catch(Exception $e){
            Log::error("[Error while getting value] : ".$e->getMessage());
            return false;
        }
    }
    public function getODCConnection(){
        try{
            $host = env('SSQL_HOST');
            $db = env('SSQL_DATABASE');
            $uname = env('SSQL_USERNAME');
            $pwd = env('SSQL_PASSWORD');
            return odbc_connect("Driver={SQL Server};Server=$host;Database=$db;",$uname,$pwd);
        }catch(Exception $e){
            Log::error("[ODBC Connection Error] - ".$e->getMessage());
            return false;
        }
    }
}
