<?php

namespace App\Http\Controllers;

use App\Models\DisLog;
use App\Models\MeasurementLog;
use App\Models\Parameter;
use App\Models\Stack;
use App\Models\ValueLog;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    public function index($stackId = null){
        $stacks = Stack::all();
        $stackActive = $stackId == null ? Stack::orderBy("id","desc")->first() : Stack::find($stackId);
        if(empty($stackActive)){
            return redirect()->route("master.stack.index")->withError("Stack tidak ada, harap tambah stack terlebih dahulu");
        }
        $parameters = Parameter::with("unit:id,name")
        ->whereRaw("stack_id = '{$stackActive->id}'")
        ->orderBy("parameter_id")
        ->get(["id","name","parameter_id","sispek_code","max_value","unit_id"]);
        return view("dashboard.index", compact('stacks','stackActive','parameters'));
    }
    public function getValueLogs($stackId){
        try{
            $valueLogs = ValueLog::with(['parameter:parameter_id,name,p_type,stack_id,rounding,max_value','parameter.stack:id,oxygen_reference'])
                ->whereRaw("parameter_id in (select parameter_id from parameters where stack_id = $stackId)")
                ->get();
            return response()->json($valueLogs);
        }catch(Exception $e){
            return response()->json(["errors"=>$e->getMessage()],400);
        }
    }

    public function getValueSent($stackId, Request $request){
        try{
            if($request->filled("parameter_id")){
                $parameterIds = $request->get("parameter_id");
                $disLogs = DisLog::with(['parameter:parameter_id,unit_id,stack_id,name','parameter.stack:id,code', 'parameter.unit:id,name'])
                    ->whereRaw("parameter_id in (".implode(",",$parameterIds).")")
                    ->whereRaw("is_sent_sispek=1")
                    ->orderBy("time_group","desc")
                    ->limit(10)
                    ->get();
            }else{
                $disLogs = DisLog::with(['parameter:parameter_id,unit_id,stack_id,name','parameter.stack:id,code', 'parameter.unit:id,name'])
                    ->whereRaw("parameter_id in (select parameter_id from parameters where stack_id = '{$stackId}')")
                    ->whereRaw("is_sent_sispek=1")
                    ->orderBy("time_group","desc")
                    ->limit(10)
                    ->get();
            }
            return DataTables::of($disLogs)->toJson();
        }catch(Exception $e){
            return response()->json(["error"=>$e->getMessage()],400);
        }
    }

    public function getLineChart($parameterId){
        try{
            $valueLogs =  MeasurementLog::select("value")->whereRaw("parameter_id = '{$parameterId}'")
                ->orderBy("xtimestamp","desc")
                ->limit(5)->pluck("value");
            return response()->json($valueLogs);
        }catch(Exception $e){
            return response()->json(["error"=>$e->getMessage()],400);
        }
    }
}
