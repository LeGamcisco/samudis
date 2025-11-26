<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MeasurementLog;
use App\Models\ValueLog;
use Exception;
use Illuminate\Http\Request;

class ValueLogsController extends Controller
{
    public function store(Request $request){
        try{
            $request->validate([
                'parameter_id' => 'required',
                'data' => 'required',
                'date_data' => 'required',
                'corrective' => 'nullable',
                'voltage' => 'nullable',
                'unit_id' => 'nullable',
            ]);
            $matchCase =  [
                'parameter_id' => $request->parameter_id,
                'xtimestamp' => $request->date_data,
            ];
            $data = [
                'parameter_id' => $request->parameter_id,
                'unit_id' => $request->unit_id ?? 1,
                'value' => $request->data,
                'voltage' => $request->voltage ?? 0,
                'corrective' => $request->data,
                'is_das_log' => 0,
                'xtimestamp' => $request->date_data,
            ];
            $logs = MeasurementLog::updateOrCreate($matchCase,$data);
            ValueLog::updateOrCreate([
                'parameter_id' => $request->parameter_id,
            ],[
                'measured' => $request->data,
                'corrective' => $request->corrective ?? 0,
            ]);
            return response()->json(['status' => 200,'success' => "Success insert data", "data" => $logs]);
        }catch(Exception $e){
            return response()->json(['status' => 500,'success' => "Error : {$e->getMessage()}"],500);
        }
    }
}
