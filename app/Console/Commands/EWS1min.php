<?php

namespace App\Console\Commands;

use App\Models\ErrorLog;
use App\Models\MeasurementLog;
use App\Models\Parameter;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Request;

class EWS1min extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:EWS1min';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking Early Warning System every minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
		//exit();
        $path = "data-one-minute";
        $execTime = now();
        $typeId = 1;
        $parameters = Parameter::whereRaw("sispek_code != ''")->with(["unit:id,name","stack:id,ews_code"])->get(["parameter_id","ews_code","unit_id","stack_id","name","max_value","is_priority","status_id"]);
        $twoMinuteAgo = now()->subMinutes(2)->format("Y-m-d H:i:00");
        $now = now()->format("Y-m-d H:i:00");
        foreach ($parameters as $parameter) {
            $data = [
                "type_id" => $typeId,
                "parameter_code" => $parameter->ews_code,
                "stack_code" => $parameter->stack->ews_code,
                "status" => $parameter->status_id,
                "time_group" => $now,
            ];
            // Where Clause
            $where = "parameter_id='{$parameter->parameter_id}' and xtimestamp >= '{$twoMinuteAgo}'";
            $isExist = (bool) MeasurementLog::whereRaw($where)->exists();
            /**
             * Check is data measurement_logs from DAS exist
             */
            if(!$isExist){
                $data = $data + [
                    'error_id' => 101, 
                    "corrective" => null,
                    "measured" => null
                ];
				
                sentToEWS($path, $data);
            }else{
                $values = MeasurementLog::with('parameter:parameter_id,name,status_id')->whereRaw($where)->get(["parameter_id","value","corrective","xtimestamp"]);
                foreach ($values as $value) {
                    /**
                     * Check Status Data
                     * */
                    $isAbnormal = ($value->value<=0.0000001);
                    if($isAbnormal){
                        $data = $data + [
                            'error_id' => 102, 
                            "measured" => $value->value,
                            "corrective" => $value->corrective,
                            "time_group" => $value->xtimestamp
                        ];
                        sentToEWS($path, $data);
                    }
                    /**
                     * Check Bakumutu Data
                     * */
                    $isOver = ($value->value >= $parameter->max_value);
                    if($isOver){
                        $data = $data + [
                            'error_id' => 103, 
                            "corrective" => $value->corrective,
                            "measured" => $value->value,
                            "time_group" => $value->xtimestamp,
                        ];
                        sentToEWS($path, $data);
                    }
                }
            }
        }
        foreach ($parameters as $parameter) {
            $getParameter = getParameterEws($parameter->ews_code);
            if($getParameter['success']){
                $parameterEws = $getParameter["data"];
                if($parameter->is_priority == 1){
                    $request = updateParameterEWS($parameter->ews_code, [
                        'status_id' => $parameter->status_id,
                        'is_priority' => 0
                    ]);
                    if($request['success']){
                        Parameter::whereRaw("parameter_id='{$parameter->parameter_id}'")->update([
                            'is_priority' => 0
                        ]);
                    }
                }else{
                    if($parameterEws['is_priority'] == 1){
                        Parameter::whereRaw("parameter_id='{$parameter->parameter_id}'")->update([
                            'status_id' => $parameterEws['status_id'],
                            'is_priority' => 0
                        ]);
                        // Update EWS Priority
                        $request = updateParameterEWS($parameter->ews_code, [
                            'is_priority' => 0
                        ]);
                    }
                }
            }
        }

        $this->info("Done in {$execTime->diffInMilliseconds()} ms\n");

    }
}