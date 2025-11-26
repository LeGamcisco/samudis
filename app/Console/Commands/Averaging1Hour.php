<?php

namespace App\Console\Commands;

use App\Models\DisLog;
use App\Models\Measurement;
use App\Models\MeasurementLog;
use App\Models\Parameter;
use App\Models\ScheduleStatus;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class Averaging1Hour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:averaging-1hour {startTime?} {endTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Averaging data 1 minutes for 1 hour (DIS Logs)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sTimeExec = now();
        // Preparing variable for backdate
        $startTime = $this->argument('startTime');
        $endTime = $this->argument('endTime');
        
        $oneHourAgo = now()->subHour()->format('Y-m-d H:i:00');
        $now = now()->format("Y-m-d H:i:00");

        $start = $startTime ?? $oneHourAgo;
        $end = $endTime ?? $now;
        if(Carbon::parse($start)->format("i") != "00" || Carbon::parse($end)->format("i") != "00"){
            throw new Exception("Start and End time must be 00");
        }
        $this->doAveraging($start, $end);
        $this->info("Averaging done in {$sTimeExec->diffInMilliseconds()} ms\n");
    }

    public function doAveraging($startTime, $endTime){
        $this->info("Start averaging {$startTime} <= {$endTime}\n");
        $parameters = Parameter::with("stack:id,oxygen_reference")
            ->get(["id","parameter_id","p_type","name","rounding","status_id","unit_id","stack_id"]);
        foreach ($parameters as $parameter) {
            $sTimeExec = now();
            // Check Scheduled status
            $statusId = null;
            $schedules = ScheduleStatus::whereRaw("end_at >= '{$startTime}' and parameter_id = '{$parameter->parameter_id}'")
            ->limit(50)
            ->get();
            foreach ($schedules as $schedule) {
                if(Carbon::parse($startTime)->isAfter(Carbon::parse($schedule->start_at)) && Carbon::parse($endTime)->isBefore(Carbon::parse($schedule->end_at))){
                    $statusId = $schedule->status_id;
                }else{
                    continue;
                }
            }
            // Variable correction factor
            $o2StackReference = $parameter->stack->oxygen_reference;
            $o2Value = $this->getO2Value($parameter->stack_id, $startTime, $endTime);
            // Where Query
            $whereRaw = "time_group >= '{$startTime}' AND time_group <= '{$endTime}' AND parameter_id = '{$parameter->parameter_id}'";
            // Check is data measurement_logs from DAS exist
            $isExist = DisLog::whereRaw("$whereRaw AND is_averaged = 0")->exists();
            if(!$isExist){
                $this->warn("No data exist [{$parameter->name}]. Skipping...\n");
                // Alarm function
                continue;
            }
            // Averaging value
            $avg = DisLog::whereRaw("{$whereRaw} AND is_averaged = 0")->avg("value");
			$avg_corrected = DisLog::whereRaw("{$whereRaw} AND is_averaged = 0")->avg("value_correction");
            $recordsTotal = DisLog::whereRaw("{$whereRaw} AND is_averaged = 0")->count();
            // Preparing data to insert dis_logs table
            $data = [
                'time_group' => $endTime,
                'measured_at' => now()->format("Y-m-d H:i:s"),
                'avg_time_group' => now()->format("Y-m-d H:i:s"),
                'parameter_id' => $parameter->parameter_id,
                'data_status_id' => $statusId ?? $parameter->status_id,
                'condition_id' => $parameter->status_id,
                'unit_id' => $parameter->unit_id,
                'value' => round($avg,$parameter->rounding),
                'value_correction' => round($avg_corrected, $parameter->rounding),
                'records_total' => $recordsTotal
            ];
            /**
             * main = corrected
             * support = not corrected or averaging only
             * o2 = value for correction
             * Formula Correction =  $avg * (21 - $o2StackReference) / (21 - $o2Value)
             */
            if($parameter->p_type == "main"){
                $corrective = ($avg * (21 - $o2StackReference) / (21 - $o2Value));
                $data["value_correction"] = round($corrective, $parameter->rounding);
            }
            $matchCase = ['parameter_id' => $parameter->parameter_id, 'time_group' => $endTime];
            if(Measurement::updateOrCreate($matchCase, $data)){
                DisLog::whereRaw($whereRaw)->update(["is_averaged" => 1]);
                $executionTime = $sTimeExec->diffInMilliseconds();
                $this->info("Success averaging [{$parameter->name}] in {$executionTime} ms\n");
            }
        }
    }

    public function getO2Value($stackId, $startTime, $endTime){
        try{
            $avgO2 = DisLog::whereRaw("time_group >= '{$startTime}' AND time_group <= '{$endTime}' AND parameter_id IN (select parameter_id from parameters where stack_id = '{$stackId}' and p_type = 'o2')")
            ->avg("value");
            $this->info("o2 value: {$avgO2}\n");
            return $avgO2;
        }catch(Exception $e){
            return 0;
        }
    }
}
