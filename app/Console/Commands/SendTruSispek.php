<?php

namespace App\Console\Commands;

use App\Models\Configuration;
use App\Models\DisLog;
use App\Models\Sispek;
use App\Models\Stack;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTruSispek extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-trusispek';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending data interval 5 minutes to TruSispek (Trusur SISPEK).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = getTokenEWS();
        $headers = [
            "Authorization" => "Bearer " . $token,
            "Api-Key" => "Bearer " . $token,
            "key" =>  "Bearer " . $token,
            "Content-Type" => "application/json"
        ];
        $oneDayAgo = now()->subDay()->format("Y-m-d H:i:00");
        $now = now()->format("Y-m-d H:00:00");
        $stacks = Stack::get();
        $egatewayCode = Configuration::first()->egateway_code ?? "egateway";
        foreach ($stacks as $stack) {
            // Where Clause Query
            $where = "is_sent_cloud = '0' AND time_group > '{$oneDayAgo}' AND time_group < '{$now}' AND parameter_id IN (
                SELECT parameter_id FROM parameters WHERE stack_id = '$stack->id'
            )";
            // Get Query
            $disLogs = DisLog::selectRaw("value_correction, parameter_id, time_group, data_status_id")
                ->with(["parameter:parameter_id,unit_id,sispek_code"])
                ->whereRaw($where)->orderBy("time_group")->get();
            foreach ($disLogs as $log) {
				// Penentuan nilai yang terkirim
				switch($log->data_status_id){
					// Validation for Abnormal or Cal.Test
					case 2:
					case 3:
                        $value = 1;		
						break;
					// Validation for Broken
					case 4:
						$value = 0;
						break;
					// Validation for Normal Status
					case 1:
					default:
						$value = $log->value_correction;
						break;
				}
                try{
                    // $url = "https://api-cems.trusur.tech/api/send/measurement";
                    $url = ENV("EWS_URL")."/send/measurement";
                    $response = Http::withHeaders($headers)->post($url,[
                        "egateway_code" => $egatewayCode,
                        "measured_at" => $log->time_group,
                        "client_parameter_id" => $log->parameter->parameter_id,
                        "unit_id" =>$log->parameter->unit_id,
                        "value" => $value,
                    ])->object();
                    if($response->status == 200){
                        $this->info("Success TruSispek: ".$log->time_group);
                    }
                }catch(Exception $e){
                    // Log::error("Error TruSispek: ".$e->getMessage());
                    $this->error($e->getMessage());
                }
            }
            $disLogs->update(["is_sent_cloud" => 1]);
        }
        $this->info("Sending ".$disLogs->count()." data");
    }

    public function getFlowrate($timeGroup){
        $disLog = DisLog::whereRaw("time_group = '{$timeGroup}' and parameter_id = 4")->first();
        if(!$disLog){ //if empty
            return 1;
        }
        switch ($disLog->data_status_id) {
            case 2:
            case 3:
                $value = 1; //Abnormal or Cal. Test
                break;
            case 4:
                $value = 0; //Broken
            default:
                $value = @$disLog->value;
                break;
        }
        return $value;
    }
}
