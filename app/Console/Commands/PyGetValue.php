<?php

namespace App\Console\Commands;

use App\Models\MeasurementLog;
use App\Models\Parameter;
use App\Models\ValueLog;
use Carbon\Carbon;
use DivisionByZeroError;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use ParseError;

class PyGetValue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:py-get-value';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Integration with PyServer PT Vale Indonesia (INCO)';

    /**
     * Execute the console command.
     */
    public $baseUrl  = "https://sorpimappp01";
    //public $baseUrl  = "http://trusur-egateway.test/api";
    public function handle()
    {
        try{

            $parameters = Parameter::whereRaw("web_id is not null")->orderBy("id")->get();
            foreach ($parameters as $parameter) {
                $webId = $parameter->web_id;
                $request = Http::withHeaders(["accpet" => "application/json"])
                    ->withOptions(['verify' => false])
                    ->get("{$this->baseUrl}/streams/{$webId}/value?selectedFields=Timestamp;Value");
                if($request->failed()){
                    $this->error("Cant get value {$parameter->name}: ".$request->body());
                    continue;
                }
                $response = $request->object();
                $value = $response->Value ?? 0;
                try{
                    if(!empty($parameter->formula)){
                        eval("\$value = {$parameter->formula};");
                    }
                    $this->info("{$parameter->name}: {$value}");
                }catch(Exception | DivisionByZeroError | ParseError $e){
                    $this->warn("Formula Error: {$parameter->name} :".$e->getMessage());
                    $value = $value;
                }
                $timestamp = Carbon::parse($response->Timestamp)->setTimezone("Asia/Makassar")->format("Y-m-d H:i:s");
                $data = [
                    // 'instrument_id' => $parameter->instrument_id,
                    // 'corrective' => $value,
                    'parameter_id' => $parameter->parameter_id,
                    'unit_id' => $parameter->unit_id ?? 1,
                    'value' => $value,
                    'voltage' => $value,
                    'is_averaged' => 0,
                    'is_das_log' => 0,
                    'xtimestamp' => $timestamp,
                ];
                MeasurementLog::updateOrCreate([
                    'parameter_id' => $parameter->parameter_id,
                    'xtimestamp' => $timestamp,
                ],$data);
                ValueLog::updateOrCreate([
                    "parameter_id" => $parameter->parameter_id,
                ],[
                    "measured" => $value,
                    "corrective" => $value,
                    "xtimestamp" => $timestamp
                ]);

            }
        }catch(Exception $e){
            $this->error("Error PyGetValue: ".$e->getMessage());
        }
    }

    public function getTemperature(int $stackId):float{
        $tempParameter = Parameter::whereRaw("stack_id=$stackId and p_type = 'temp'")->first();
        if(!$tempParameter) {
            $this->warn("Temperature parameter not found");
            return 0.0;
        }
        $value = $this->getValueByWebId($tempParameter->web_id);
        if(!empty($tempParameter->formula)){
            try{
                eval("\$value = {$tempParameter->formula};");
                return $value;
            }catch(Exception $e){
                $this->warn("Formula Error: {$tempParameter->name}".$e->getMessage());
                return $value;
            }
        }
        return $value;
    }

    public function getPressure(int $stackId):float{
        $pressureParameter = Parameter::whereRaw("stack_id=$stackId and p_type = 'temp'")->first();
        if(!$pressureParameter) {
            $this->warn("Pressure parameter not found");
            return 0.0;
        }
        $value = $this->getValueByWebId($pressureParameter->web_id);
        if(!empty($pressureParameter->formula)){
            try{
                eval("\$value = {$pressureParameter->formula};");
                return $value;
            }catch(Exception $e){
                $this->warn("Formula Error: {$pressureParameter->name}".$e->getMessage());
                return $value;
            }
        }
        return $value;
    }

    public function getValueByWebId($webId){
        try{
            $request = Http::withHeaders([
                "accept" => "application/json"
            ])
            ->withOptions(['verify' => false])
            ->get("{$this->baseUrl}/streams/{$webId}/value?selectedFields=Timestamp;Value");
            if($request->failed()){
                $this->error("(getValueByWebId): Cant get value {$webId}: ".$request->body());
                return 0.0;
            }
            $response = $request->object();
            return $response->Value ?? 0.0;
        }catch(Exception $e){
            return 0.0;
        }
    }
}
