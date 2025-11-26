<?php

namespace App\Console\Commands;

use App\Models\DisLog;
use App\Models\ErrorLog;
use App\Models\MeasurementLog;
use App\Models\Parameter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class EWS5min extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:EWS5min';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking Early Warning System every 5 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $parameters = Parameter::with(["stack:id,ews_code"])->whereRaw("sispek_code != ''")->get(["parameter_id","status_id","name","max_value","stack_id","ews_code"]);
        $FiveMinutes = now()->subMinutes(6)->format("Y-m-d H:i:00");
        $now = now()->format("Y-m-d H:i:00");
        foreach ($parameters as $parameter) {
            $data = ['type_id' => 2, 'parameter_id' => $parameter->parameter_id];
            $where = "parameter_id='{$parameter->parameter_id}' and time_group >= '{$FiveMinutes}'";
            $isExist = DisLog::whereRaw($where)->exists();
            /**
             * Check is data measurement_logs from DAS exist
             */
            if(!$isExist){
				$this->info("Data not exist");
                // check to the measurements logs
                $isLogExist = MeasurementLog::whereRaw("parameter_id='{$parameter->parameter_id}' and xtimestamp >= '{$FiveMinutes}' and is_das_log=0")->exists();
                if($isLogExist){
					//print_r($isLogExist);
                    // Averaging data if data measurement_logs is exist
                    Artisan::call("app:averaging-5min '{$FiveMinutes}' '{$now}'\n");
                }else{
                    sentToEWS("data-five-minute", $data + [
                        "error_id" => 201, // Data not available
                        "stack_code" => $parameter->stack->ews_code,
                        "parameter_code" => $parameter->ews_code,
                        "status" => $parameter->status_id,
                        "corrective" => null,
                        "measured" => null,
                        "time_group" => $now
                    ]);
                }
            }else{
                $values = DisLog::whereRaw($where)->get(["value","value_correction","data_status_id","time_group"]);
				$this->info("Checking : ".count($values)." data");
                /**
                 * Looping data dis_logs values
                 */
                foreach ($values as $value) {
                    /**
                     * Check Status Data
                     * */
                    $isAbnormal = ($value->value_correction<=0.0000001);
                    if($isAbnormal){
                        sentToEWS("data-five-minute", $data + [
                            "error_id" => 202,
                            "stack_code" => $parameter->stack->ews_code,
                            "parameter_code" => $parameter->ews_code,
                            "status" => $value->data_status_id,
                            'measured' => $value->value,
                            'corrective' => $value->value_correction,
                            "time_group" => $value->time_group
                        ]);
                    }
                    /**
                     * Check Bakumutu Data
                     * */
                    $isOverange = ($value->value_correction >= $parameter->max_value);
                    if($isOverange){
                        sentToEWS("data-five-minute", $data + [
                            "error_id" => 203,
                            "stack_code" => $parameter->stack->ews_code,
                            "parameter_code" => $parameter->ews_code,
                            "status" => $value->data_status_id,
                            'measured' => $value->value,
                            'corrective' => $value->value_correction,
                            "time_group" => $value->time_group
                        ]);
                    }
                }

            }
        }
    }
}
