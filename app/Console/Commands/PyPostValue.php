<?php

namespace App\Console\Commands;

use App\Models\DisLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PyPostValue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:py-post-value';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'POST [DIS Logs](5 Min Average Data) Value to PyServer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            $baseUrl  = "https://sorpimappp01";
            $baseUrl = "http://127.0.0.1:8000/api";

            $disLogs = DisLog::with([
                    "Parameter:id,parameter_id,unit_id,name,web_id_post",
                    "Parameter.Unit:id,name"
                ])
                // ->whereRaw("is_sent_pi = 0")
                ->whereRaw("is_sent_cloud = 0")
                ->limit(10)
                ->get();
            $this->info("Found {$disLogs->count()} DisLogs");
            foreach($disLogs as $disLog){
                if(empty($disLog->Parameter)){
                    $this->warn("Cant post value (parameter_id=$disLog->parameter_id): No Parameter");
                    continue;
                }
                $webId = $disLog->Parameter->web_id_post;
                switch ($disLog->data_status_id) {
                    case 2:
                    case 3:
                        $correctionValue = 1;
                        break;
                    case 4:
                        $correctionValue = 0;
                        break;
                    case 1:
                    default:
                        $correctionValue = $disLog->value_correction;
                        break;
                }
                $params = [
                    "Timestamp" => Carbon::parse($disLog->time_group)->setTimezone("UTC")->format("Y-m-d\TH:i:s\Z"),
                    "UnitsAbbreviation" => $disLog->Parameter->Unit->name ?? "",
                    "Good" => ($correctionValue < 0 ? false : true),
                    "Questionable" => ($correctionValue < 0 ? false : true),
                    "Value" => $correctionValue,
                ];
                $request = Http::withBody(json_encode($params), 'application/json')
                    ->post("{$baseUrl}/streams/{$webId}/value",$params);
                if($request->failed()){
                    $this->error("Cant post value {$disLog->Parameter->name}: ".$request->body());
                }
                $response = $request->object();
                if(in_array($request->status(),[202,204])){
                    $disLog->update([
                        "is_sent_cloud" => 1,
                        "sent_cloud_at" => now()
                    ]);
                }
            }
        }catch(Exception $e){
            $this->error("PyPOSTValue Error: ".$e->getMessage());
        }
    }
}
