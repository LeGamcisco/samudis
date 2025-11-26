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

class Averaging5Min extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:averaging-5min {startTime?} {endTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Averaging data 1 minutes for 5 minutes (DIS Logs)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sTimeExec = now();
        // Preparing variable for backdate
        $startTime = $this->argument('startTime');
        $endTime = $this->argument('endTime');
        
        $fiveMinutesAgo = now()->subMinutes(5)->format('Y-m-d H:i:00');
        $now = now()->format("Y-m-d H:i:00");

        $interval = 5; //mins
        $start = $startTime ?? $fiveMinutesAgo;
        $start = roundMinute($start, $interval);
        $end = $endTime ?? $now;
        $end = roundMinute($end, $interval);

        $this->doAveraging($start, $end);
        $this->info("Averaging done in {$sTimeExec->diffInMilliseconds()} ms\n");
    }

    public function doAveraging($startTime, $endTime){
        $this->info( "Start averaging {$startTime} <= {$endTime}\n");
        // Average O2
        $o2Parameters = Parameter::whereRaw("p_type = 'o2'")->get();
        foreach ($o2Parameters as $o2Parameter) {
            $parameterId = $o2Parameter->parameter_id;
            $where = "xtimestamp > '{$startTime}' and xtimestamp <= '{$endTime}' and parameter_id = '{$parameterId}' and is_das_log = 0";
            $avg = MeasurementLog::whereRaw($where)->avg("value");
            $statusId = $this->getStatusId($parameterId, $startTime, $endTime) ?? $o2Parameter->status_id;
            $data = [
                'time_group' => $endTime,
                'measured_at' => now()->format("Y-m-d H:i:s"),
                'avg_time_group' => $endTime,
                'parameter_id' => $parameterId,
                'data_status_id' => $statusId,
                'condition_id' => $o2Parameter->status_id,
                'unit_id' => $o2Parameter->unit_id,
                'value' => round($avg,$o2Parameter->rounding),
                'value_correction' => round($avg,$o2Parameter->rounding),
            ];
            DisLog::updateOrCreate(["time_group" => $endTime, "parameter_id" => $parameterId], $data);
            MeasurementLog::whereRaw($where)->update(["is_das_log" => 1]);
        }

        // Average other parameters
        $parameters = Parameter::with("stack:id,oxygen_reference")
            ->whereRaw("p_type != 'o2'")
            ->get();
        foreach ($parameters as $parameter) {
            $sTimeExec = now();
            $statusId = $this->getStatusId($parameter->parameter_id, $startTime, $endTime) ?? $parameter->status_id;
            $parameterId = $parameter->parameter_id;
            // Variable correction factor
            $o2StackReference = $parameter->stack->oxygen_reference;
            $o2Value = $this->getO2Value($parameter->stack_id, $endTime);
            // Where Query
            $whereRaw = "xtimestamp > '{$startTime}' AND xtimestamp <= '{$endTime}' AND parameter_id = '{$parameterId}'";
            // Match Case
            $matchCase = ['parameter_id' => $parameter->parameter_id, 'time_group' => $endTime];
            // Check is data measurement_logs from DAS exist
            $isExist = MeasurementLog::whereRaw("$whereRaw AND is_das_log = 0")->get()->count();
			$isExist = $isExist > 0 ? true:false;
            if(!$isExist){
                $this->warn("No data exist [{$parameter->name} in {$endTime}].\n");
                // Alarm function
                $data = [
                    'time_group' => $endTime,
                    'measured_at' => now()->format("Y-m-d H:i:s"),
                    'avg_time_group' => $endTime,
                    'parameter_id' => $parameter->parameter_id,
                    'data_status_id' => $statusId ?? $parameter->status_id,
                    'condition_id' => $parameter->status_id,
                    'unit_id' => $parameter->unit_id,
                    'value' => 0,
                    'value_correction' => 0
                ];
                if(DisLog::updateOrCreate($matchCase, $data)){
                    MeasurementLog::whereRaw($whereRaw)->update(["is_das_log" => 1]);
                    $executionTime = $sTimeExec->diffInMilliseconds();
                    $this->info("Success averaging [{$parameter->name}] in {$executionTime} ms\n");
                }

                continue;
            }
            // Averaging value
            $avg = MeasurementLog::whereRaw("{$whereRaw} AND is_das_log = 0")->avg("value");
			$avg_corrected = MeasurementLog::whereRaw("{$whereRaw} AND is_das_log = 0")->avg("value"); //corrective
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
                'value_correction' => round($avg_corrected, $parameter->rounding)
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
            if(DisLog::updateOrCreate($matchCase, $data)){
                MeasurementLog::whereRaw($whereRaw)->update(["is_das_log" => 1]);
                $executionTime = $sTimeExec->diffInMilliseconds();
                $this->info("Success averaging [{$parameter->name}] in {$executionTime} ms\n");
            }
        }
    }

    public function getStatusId($parameterId, $startTime, $endTime){
        $statusId = null;
		return $statusId;
        // Check Scheduled status
        $schedules = ScheduleStatus::whereRaw("end_at >= '{$startTime}' and parameter_id = '{$parameterId}'")
            ->limit(50)
            ->get();
        foreach ($schedules as $schedule) {
            if(Carbon::parse($startTime)->isAfter(Carbon::parse($schedule->start_at)) && Carbon::parse($endTime)->isBefore(Carbon::parse($schedule->end_at))){
                $statusId = $schedule->status_id;
            }else{
                continue;
            }
        }
        return $statusId;
    }

    public function getO2Value($stackId, $timeGroup){
        try{
            $o2 = DisLog::whereRaw("time_group = '{$timeGroup}' AND parameter_id IN (select parameter_id from parameters where stack_id = '{$stackId}' and p_type = 'o2')")
                ->avg("value_correction");
            return $o2;
        }catch(Exception $e){
            return 0;
        }
    }

    public function roundingMinute($hour, $minutes = '5', $format = "Y-m-d H:i:00") {
        $seconds = strtotime($hour);
        $rounded = round($seconds / ($minutes * 60)) * ($minutes * 60);
        return date($format, $rounded);
    }

}
