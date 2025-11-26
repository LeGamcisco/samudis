<?php

namespace App\Console\Commands;

use App\Models\DisLog;
use App\Models\ErrorLog;
use App\Models\Parameter;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Request;

class EWS10min extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:EWS10min';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Pengiriman data ke SISPEK';

    /**
     * Execute the console command.
     */
    public function handle()
    {
		#exit();
        $startAt = now()->subHour()->format("Y-m-d H:00:00");
        $endAt = now()->subHour()->format("Y-m-d H:55:00");
        $parameters = Parameter::whereRaw("sispek_code != ''")->get();
        foreach ($parameters as $parameter) {
            $where = "parameter_id = '{$parameter->parameter_id}' and time_group >= '{$startAt}' and time_group <= '{$endAt}' and is_sent_sispek = '0'";
            $logs = DisLog::whereRaw($where)->get(['value','value_correction','time_group']);
            foreach ($logs as $value) {
                $data = [
                    "error_id" => 304,
                    "type_id" => 2,
                    "parameter_id" => $parameter->parameter_id,
                    "stack_code" => $parameter->stack->ews_code,
                    "parameter_code" => $parameter->ews_code,
                    "status" => $parameter->status_id,
                    'measured' => $value->value,
                    'corrective' => $value->value_correction,
                    "time_group" => $value->time_group
                ];
                sentToEWS("data-five-minute", $data);
            }
        }
    }
}
