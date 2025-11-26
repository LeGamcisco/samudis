<?php

namespace App\Http\Controllers\RawData;

use App\Exports\DisLogsExport;
use App\Http\Controllers\Controller;
use App\Models\DisLog;
use App\Models\Parameter;
use App\Models\Stack;
use App\Models\Status;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisLogController extends Controller
{
    public function index(){
        $tables = getTable("dis_logs");
        $stacks = Stack::get();
        $statuses = Status::get();
        return view('dis-logs.index', compact('tables','stacks','statuses'));
    }

    public function unsent(Request $request){
        try{
            $data = $this->validate($request, [
                'parameter_id' => 'nullable|array',
                'parameter_id.*' => 'required|numeric',
                'status_id' => 'required',
                'datetime_start' => 'required',
                'datetime_end' => 'required',
            ],[
                'parameter_id.*.required' => 'At least 1 must be selected!',
            ]);
            $isBefore23hours = now()->subHours(23)->isBefore(Carbon::parse($data['datetime_start']));
            if(!$isBefore23hours){
                return response()->json(['success' => false, 'message' => 'Cannot unsent data more than 23 hours before now']);
            }
            $parameters = $data['parameter_id'] ?? Parameter::pluck('parameter_id')->toArray();
            foreach ($parameters as $parameterId) {
                $where = "parameter_id='{$parameterId}' and time_group >= '{$data['datetime_start']}' and time_group <= '{$data['datetime_end']}'";
                DisLog::whereRaw($where)->update([
                    'data_status_id' => $data['status_id'],
                    'is_sent_sispek' => 0,
                    'sent_sispek_at' => null
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Successfully update data to unsent']);

        }catch(Exception $e){
            return response()->json(['success'=>false,'message' => $e->getMessage()]);
        }
    }

    public function datatable(Request $request){
        $query = $this->getFilterQuery($request);
        return datatables()->of($query)->toJson();
    }

    public function export(Request $request){
        $query = $this->getFilterQuery($request);
        $date = date("YmdHis");
        return (new DisLogsExport($query))->download($date.' - Raw DIS Logs.xlsx');
    }

    public function getFilterQuery($request){
        $whereRaw = "1=1";
        $filterFields = ["data_status_id","datetime_start","datetime_end","is_sispek_sent"];
        if(config('database.default') == 'pgsql'){
            $tableName = $request->data_source ?? 'dis_logs';
        }else{
            $tableName = $request->data_source ?? 'das_logs';
        }
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
            $id = "";
            foreach ($request->parameter_id as $parameter_id) {
                $id.="{$parameter_id},";
            }
            $id = rtrim($id,",");
            if(!empty($id)) $whereRaw .= " AND $tableName.parameter_id IN ({$id})";
        }
        if($request->filled("is_sent_sispek")){
            $whereRaw .= " AND is_sent_sispek = '$request->is_sent_sispek'";
        }
        return DB::table($tableName)
            ->select(["$tableName.*","parameters.name as parameter_name","stacks.code as stack_name","statuses.name as status_name","units.name as unit_name"])
            ->leftJoin('parameters',"$tableName.parameter_id",'parameters.parameter_id')
            ->leftJoin('stacks','parameters.stack_id','stacks.id')
            ->leftJoin('statuses','data_status_id','statuses.id')
            ->leftJoin('units',"parameters.unit_id",'units.id')
            ->whereRaw($whereRaw);
    }
}
