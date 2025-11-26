<?php

namespace App\Console\Commands;

use App\Models\DisLog;
use App\Models\ErrorLog;
use App\Models\Measurement;
use App\Models\MeasurementLog;
use App\Models\Parameter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class EWS1Hour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:EWS1hour';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Early warning system 1 hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $parameters = Parameter::whereRaw("sispek_code != ''")->get(["parameter_id","name","max_value"]);
        $oneHourAgo = now()->subHour()->format("Y-m-d H:i:00");
        $now = now()->format("Y-m-d H:i:00");
        foreach ($parameters as $parameter) {
            $data = ['type_id' => 2, 'parameter_id' => $parameter];
            $where = "parameter_id='{$parameter->parameter_id}' and time_group >= '{$oneHourAgo}'";
            $isExist = Measurement::whereRaw($where)->exists();
            /**
             * Check is data measurement_logs from DAS exist
             */
            $with = ['parameter:parameter_id,stack_id,ews_code','parameter.stack:id,ews_code'];
            if(!$isExist){
                // check to the measurements logs
                $isLogExist = DisLog::whereRaw("parameter_id='{$parameter->parameter_id}' and time_group >= '{$oneHourAgo}' and is_averaged=0")->exists();
                if($isLogExist){
                    Artisan::call("app:averaging-1hour '{$oneHourAgo}' '{$now}'\n");
                }else{
                    echo "[{$parameter->name}] is not available on {$oneHourAgo} - {$now}";
                    sentToEWS("data-one-hour", $data + [
                        'error_id' => 301, 
                        "corrective" => null,
                        "measured" => null,
                        "time_group" => $now
                    ]);
                }
            }else{
                $values = Measurement::with($with)->whereRaw($where)->get(["data_status_id","value","value_correction","time_group"]);
                foreach ($values as $value) {
                    /**
                     * Check Status Data
                     * */
                    $isAbnormal = ($value->value_correction<=0.0000001);
                    if($isAbnormal){
                        sentToEWS("data-one-hour", $data + [
                            'error_id' => 302,
                            'parameter_code' => $value->parameter->ews_code,
                            'stack_code' => $value->parameter->stack->ews_code,
                            'status_id' => $value->data_status_id,
                            'measured' => $value->value,
                            'corrective' => $value->value_correction,
                            'time_group' => $value->time_group
                        ]);
                    }
                    /**
                     * Check Bakumutu Data
                     * */
                    $isOverange = ($value->value_correction > $parameter->max_value);
                    if($isOverange){
                        sentToEWS('data-one-hour',$data + [
                            'error_id' => 303,
                            'parameter_code' => $value->parameter->ews_code,
                            'stack_code' => $value->parameter->stack->ews_code,
                            'status_id' => $value->data_status_id,
                            'measured' => $value->value,
                            'corrective' => $value->value_correction,
                            'time_group' => $value->time_group
                        ]);
                    }
                }

            }
            /** Check keterkiriman data
             * */
            $where = "parameter_id='{$parameter->parameter_id}' and time_group >= '{$oneHourAgo}' and time_group <= '{$now}' and parameter_id in (select parameter_id from parameters where sispek_code not null) and is_sent_sispek = '0'";
            $disLog = DisLog::with($with)
                ->whereRaw($where)->get(['data_status_id','parameter_id','value','value_correction']);
            foreach ($disLog as $log) {
                sentToEWS('data-one-hour', $data + [
                    'error_id' => 304, //Data not sent
                    'parameter_code' => $log->parameter->ews_code,
                    'stack_code' => $log->parameter->stack->ews_code,
                    'status_id' => $log->data_status_id,
                    'measured' => $log->value,
                    'corrective' => $log->value_correction,
                    'time_group' => $log->time_group,
                ]);
            }
        }
    }
}
