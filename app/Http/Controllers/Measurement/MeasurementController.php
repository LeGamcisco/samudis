<?php

namespace App\Http\Controllers\Measurement;

use App\Exports\MeasurementDailyExport;
use App\Exports\MeasurementLogExport;
use App\Exports\MeasurementLogGovernmentExport;
use App\Http\Controllers\Controller;
use App\Models\Measurement;
use App\Models\Parameter;
use App\Models\Stack;
use App\Models\Status;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MeasurementController extends Controller
{
    public function index(){
        $tables = getTable("measurements");
        $stacks = Stack::get();
        $statuses = Status::get();
        return view('measurement.index', compact('tables','stacks','statuses'));
    }

    public function datatable(Request $request){
        $query = $this->getFilterQuery($request);
        return datatables()->of($query)->toJson();
    }

    public function export(Request $request){
        $query = $this->getFilterQuery($request);
        $date = date("YmdHis");
        return (new MeasurementLogExport($query))->download($date.' - 1 Hour Avg Logs.xls');
    }

    public function exportMonthly(Request $request){
        $data = $this->validate($request, [
            'month' => 'required',
            'year' => 'required',
            'data_source' => 'required',
            'stack_id' => 'required',
        ]);
        if(!$data){
            return redirect()->back()->with("error", "Data not found");
        }
        try{
            $month = $request->month;        
            $year = $request->year;        
            $stackId = $request->stack_id;  
            $tableName = $request->data_source;      
            $timeGroups = DB::table($tableName)->selectRaw("time_group")
                ->whereRaw("to_char(time_group,'YYYY-MM') = '$year-$month' and parameter_id in (select parameter_id from parameters where stack_id = '$stackId')")
                ->orderBy("time_group","asc")->groupBy("time_group")->get();
            if($timeGroups->count() < 1){
                return redirect()->back()->with("error", "Data not found");
            }
            $monthYear = Carbon::parse($year."-".$month)->format("F Y");
            $filename = Carbon::parse($year."-".$month)->format("Ym")."_CEMS_".time().".xls";
            $data = [];
            foreach ($timeGroups as $key=> $timeGroup) {
                $day = Carbon::parse($timeGroup->time_group)->format("d");
                $startAt = Carbon::parse($timeGroup->time_group)->subHour()->format("H:i");
                $endAt = Carbon::parse($timeGroup->time_group)->format("H:i");
                $data[$key]['day'] = $day;
                $data[$key]['hour'] = "$startAt-$endAt";
                $data[$key]['o2_value'] =  DB::table($tableName)->whereRaw("time_group = '$timeGroup->time_group' and parameter_id in (select parameter_id from parameters where stack_id = '$stackId' and p_type = 'o2')")->avg("value_correction");
                $data[$key]['co_value'] =  DB::table($tableName)->whereRaw("time_group = '$timeGroup->time_group' and parameter_id in (select parameter_id from parameters where stack_id = '$stackId' and lower(name) = 'co')")->avg("value_correction");
                $data[$key]['co2_value'] =  DB::table($tableName)->whereRaw("time_group = '$timeGroup->time_group' and parameter_id in (select parameter_id from parameters where stack_id = '$stackId' and lower(sispek_code) = 'karbon dioksida (co2)')")->avg("value_correction");
                $data[$key]['so2_measured'] =  DB::table($tableName)->whereRaw("time_group = '$timeGroup->time_group' and parameter_id in (select parameter_id from parameters where stack_id = '$stackId' and lower(sispek_code) = 'sulfur dioksida (so2)')")->avg("value");
                $data[$key]['so2_corrective'] =  DB::table($tableName)->whereRaw("time_group = '$timeGroup->time_group' and parameter_id in (select parameter_id from parameters where stack_id = '$stackId' and lower(sispek_code) = 'sulfur dioksida (so2)')")->avg("value_correction");
                $data[$key]['nox_measured'] =  DB::table($tableName)->whereRaw("time_group = '$timeGroup->time_group' and parameter_id in (select parameter_id from parameters where stack_id = '$stackId' and lower(sispek_code) = 'nitrogen oksida (nox)')")->avg("value");
                $data[$key]['nox_corrective'] =  DB::table($tableName)->whereRaw("time_group = '$timeGroup->time_group' and parameter_id in (select parameter_id from parameters where stack_id = '$stackId' and lower(sispek_code) = 'nitrogen oksida (nox)')")->avg("value_correction");
                $data[$key]['hg_measured'] =  DB::table($tableName)->whereRaw("time_group = '$timeGroup->time_group' and parameter_id in (select parameter_id from parameters where stack_id = '$stackId' and lower(sispek_code) = 'merkuri (hg)')")->avg("value");
                $data[$key]['hg_corrective'] =  DB::table($tableName)->whereRaw("time_group = '$timeGroup->time_group' and parameter_id in (select parameter_id from parameters where stack_id = '$stackId' and lower(sispek_code) = 'merkuri (hg)')")->avg("value_correction");
                $data[$key]['dust_measured'] =  DB::table($tableName)->whereRaw("time_group = '$timeGroup->time_group' and parameter_id in (select parameter_id from parameters where stack_id = '$stackId' and lower(sispek_code) = 'partikulat (pm)')")->avg("value");
                $data[$key]['dust_corrective'] =  DB::table($tableName)->whereRaw("time_group = '$timeGroup->time_group' and parameter_id in (select parameter_id from parameters where stack_id = '$stackId' and lower(sispek_code) = 'partikulat (pm)')")->avg("value_correction");
                $data[$key]['flow_measured'] =  DB::table($tableName)->whereRaw("time_group = '$timeGroup->time_group' and parameter_id in (select parameter_id from parameters where stack_id = '$stackId' and lower(sispek_code) = 'laju_alir')")->avg("value");
            }
            return Excel::download(new MeasurementDailyExport($data, $monthYear,$stackId), $filename);
        }catch(Exception $e){
            return redirect()->back()->with("error", "Error : ".$e->getMessage());
        }
       
    }

    public function exportGovernment(Request $request){
        $tableName = $request->data_source ?? 'measurements';
        $query = $this->getFilterQuery($request);
        $date = date("YmdHis");
        $parameter = Parameter::with("stack:id,sispek_code")->where("parameter_id",$request->parameter_id)->first();
        $filename = $date."_CEMS_".$parameter->stack->sispek_code."_".$parameter->name."_".now()->format("My").".xls";
        return (new MeasurementLogGovernmentExport($query, $tableName))->download($filename,\Maatwebsite\Excel\Excel::XLS);
    }

    public function getFilterQuery($request){
        $whereRaw = "1=1";
        $filterFields = ["data_status_id","datetime_start","datetime_end"];
        $tableName = $request->data_source ?? 'measurements';
        foreach ($filterFields as $filter) {
            if($request->filled($filter)){
                if(strpos($filter, 'datetime_') !== false){
                    if($filter == "datetime_start"){
                        $whereRaw.=" AND time_group >= '{$request->$filter}'";
                    }else{
                        $whereRaw.=" AND time_group <= '{$request->$filter}'";
                    }
                }else{
                    $whereRaw.=" AND $filter = '{$request->$filter}'";
                }
            }
        }
        if($request->filled("stack_id")){
            $whereRaw .= " AND $tableName.parameter_id IN (SELECT id from parameters where stack_id = '$request->stack_id')";
        }
        if($request->filled("parameter_id")){
            if(is_array($request->parameter_id)){
                $id = "";
                foreach ($request->parameter_id as $parameter_id) {
                    $id.="{$parameter_id},";
                }
                $id = rtrim($id,",");
                if(!empty($id)) $whereRaw .= " AND $tableName.parameter_id IN ({$id})";
            }else{
                $whereRaw .= " AND $tableName.parameter_id = '{$request->parameter_id}'";
            }
        }
        return DB::table($tableName)
            ->select(["$tableName.*","parameters.name as parameter_name","parameters.stack_id","stacks.code as stack_name","parameters.sispek_code as parameter_sispek_code","stacks.sispek_code as stack_sispek_code","stacks.code as stack_code","statuses.name as status_name","units.name as unit_name"])
            ->leftJoin('parameters',"$tableName.parameter_id",'parameters.parameter_id')
            ->leftJoin('stacks','parameters.stack_id','stacks.id')
            ->leftJoin('statuses','data_status_id','statuses.id')
            ->leftJoin('units',"parameters.unit_id",'units.id')
            ->whereRaw($whereRaw);
    }
}
