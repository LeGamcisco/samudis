<?php

namespace App\Console\Commands;

use App\Models\Measurement;
use App\Models\MeasurementLog;
use App\Models\Parameter;
use Illuminate\Console\Command;

class DemoValueLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:demo-value-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        while(true){
            $parameters = Parameter::get();
            $timestamp = date("Y-m-d H:i:s");
            $this->info($timestamp."\n");
            foreach ($parameters as $parameter) {
                $value = rand(rand(10,100),rand(100,1000));
                $data = [
                    'parameter_id' => $parameter->parameter_id,
                    'unit_id' => $parameter->unit_id ?? 1,
                    'value' => $value,
                    'voltage' =>0,
                    'corrective' => $value,
                    'is_das_log' => 0,
                    'xtimestamp' => $timestamp,
                ];
                MeasurementLog::updateOrCreate([
                    'parameter_id' => $parameter->parameter_id,
                    'xtimestamp' => $timestamp,
                ],$data);
            }
            sleep(30);
        }
    }
}
