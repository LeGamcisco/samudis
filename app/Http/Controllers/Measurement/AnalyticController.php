<?php

namespace App\Http\Controllers\Measurement;

use App\Http\Controllers\Controller;
use App\Models\Parameter;
use App\Models\Stack;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticController extends Controller
{
    public function index(){
        $tables = getTable("measurements");
        $stacks = Stack::get();
        return view("measurement.analytic", compact('tables','stacks'));
    }

    public function getData(Request $request, $parameterId){
        try{
            $parameter = Parameter::with(["unit:id,name"])
                ->where("parameter_id", $parameterId)->first(["name","max_value","unit_id"]);
            $data = $request->validate([
                'data_source' => 'nullable',
                'datetime_start' => 'nullable',
                'datetime_end' => 'nullable',
            ]);
            $tableName = $data['data_source'] ?? 'measurements';
            $timeStart = $data['datetime_start'] ?? date("Y-m-d H:i:s",strtotime("-1 day"));
            $timeEnd = $data['datetime_end'] ?? date("Y-m-d H:i:s");
            $logs = DB::table($tableName)
            ->select('time_group','value','value_correction')
            ->where(['parameter_id' =>$parameterId])
            ->whereBetween('time_group',[$timeStart, $timeEnd])
            ->orderBy("time_group", "asc")
            ->get();
            return response()->json(['success' => true,'parameter' => $parameter,'data' => $logs]);
        }catch(Exception $e){
            return response()->json(['success' => false,'errors' => [$e->getMessage()]],400);
        }

    }
}
