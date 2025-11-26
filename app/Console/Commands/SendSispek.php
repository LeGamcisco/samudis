<?php

namespace App\Console\Commands;

use App\Models\DisLog;
use App\Models\Sispek;
use App\Models\Stack;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSispek extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-sispek';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending data 5 min to sispek with interval 1 hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = $this->getToken();
        $sispek = Sispek::first();
        $headers = [
            "Authorization: Bearer " . $token,
            "Api-Key: Bearer " . $token,
            "key: Bearer " . $token,
            "cache-control: no-cache",
            "content-type: application/json"
        ];
        $oneDayAgo = now()->subDay()->format("Y-m-d H:i:00");
        $now = now()->format("Y-m-d H:00:00");
        $stacks = Stack::whereRaw("sispek_code <> ''")->get();
        $data = [];
		$whereUpdate = [];
        foreach ($stacks as $key => $stack) {
            $data[$key]["kode_cerobong"] = $stack->sispek_code;
            $data[$key]["interval"] = 5;
            $data[$key]["parameter"] = [];
            // Where Clause Query
            $where = "is_sent_sispek = '0' AND time_group > '{$oneDayAgo}' AND time_group < '{$now}' AND parameter_id IN (
                SELECT parameter_id FROM parameters WHERE sispek_code <> '' AND stack_id = '$stack->id'
            )";
            // Get Query
			$whereUpdate[] = $where;
            $disLogs = DisLog::selectRaw("value_correction, parameter_id, time_group, data_status_id")
            ->with(["parameter:parameter_id,sispek_code"])
            ->whereRaw($where)->orderBy("time_group")->get();
            $curentTimeGroup = null;
            $i = 0;
            foreach ($disLogs as $log) {
				// Penentuan nilai yang terkirim
				switch($log->data_status_id){
					// Validation for Abnormal or Cal.Test
					case 2:
					case 3:
                        $value = 1;
                        // Check is Hg
                        if($log->parameter->sispek_code == "Merkuri (Hg)" || strtolower($log->parameter->name) == "merkuri (hg)"){
                            $value = "0.0000001";
                        }		
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
				// Validation for same time_group
                if(($curentTimeGroup == $log->time_group) || $curentTimeGroup == null){
                    $data[$key]["parameter"][$i]["waktu"] = "{$log->time_group}";                    
                }else{
                    // Increment index while timegroup not same
                    $i++;
                }
				$data[$key]["parameter"][$i]["{$log->parameter->sispek_code}"] = $value;
                // $data[$key]["parameter"][$i]["laju_alir"] = $this->getFlowrate($log->time_group);
                $curentTimeGroup = $log->time_group;
            }
            if(count($data[$key]) < 1){
                // unset data if no data
                unset($data[$key]);
            }
        }
        if(empty($data)){
            return $this->error("Not sent to SISPEK! no data available!");
        }
        try{
            $body = json_encode(["data" => $data],1);
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $sispek->server.$sispek->api_post_data,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_SSL_VERIFYPEER => 0,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $body,
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $token,
					"Api-Key: Bearer " . $token,
					"key: Bearer " . $token,
					"cache-control: no-cache",
					"content-type: application/json"
				),
			));


			$response = curl_exec($curl);
			
			$message = @json_decode($response, true)["message"];
			$this->info($message);
            if(strtolower($message) == "sukses"){
				foreach($whereUpdate as $where){
					DisLog::whereRaw($where)->update([
						"is_sent_sispek" => 1,
						"sent_sispek_at" => date("Y-m-d H:i:s")
					]);
				}
                
            }else{
                $this->error($response['message']);
                Log::error($response['message'],["file" => __FILE__,"line" => __LINE__]);
            }
        }catch(Exception $e){
            $this->error($e->getMessage());
            Log::error($e->getMessage(), ["file" => __FILE__, "line" => __LINE__]);
            return null;
        }
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

    public function getToken(){
        try{
            $sispek = Sispek::first();
            $url =  "{$sispek->server}{$sispek->api_get_token}";
            $headers = [
                "Cache-Control" => "no-cache",
                "Content-Type" => "application/json"
            ];
            $body =  json_encode([
                "app_id" => $sispek->app_id,
                "app_pwd_hash" => md5($sispek->app_id.$sispek->app_secret),
            ]);
            $response = Http::withHeaders($headers)
                ->withOptions(['verify' => false])
                ->withBody($body)
                ->post($url)
                ->object();
            $token = $response?->token ?? null;
            if(is_null($token)){
                $this->error($response?->message ?? 'Cant get token!');
                Log::error($response?->message ?? 'Cant get token!',[
                    "file" => __FILE__,
                    "line" => __LINE__,
                ]);
                return null;
            }
            $sispek->update(["token" => $token]);
			//dd($sispek);
            return $token;
        }catch(Exception $e){
            $this->error($e->getMessage());
            Log::error($e->getMessage());
            return null;
        }
    }
}
