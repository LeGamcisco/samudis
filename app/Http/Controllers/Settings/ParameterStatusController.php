<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Parameter;
use App\Models\Stack;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ParameterStatusController extends Controller
{
    public function index($stackId=null){
        $stack = $stackId == null ? Stack::first() :Stack::find($stackId);
        if(empty($stack)){
            return redirect()->route("master.stack.index")->withError("Stack not found! Please input Stack Data");
        }
        $stackId = $stack->id;
        $stacks = Stack::select(['id','code'])->get();
        return view('settings.parameter', compact('stackId','stack','stacks'));
    }

    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'parameter_ids' => 'required',
                'status_id' => 'required',
                'description' => 'required',
            ],[
                'parameter_ids.required' => 'At least 1 must be selected!',
                'status_id.required' => 'Status must be selected!',
                'description.required' => 'Reason must be filled!',
            ]);
            if($validator->fails()){
                return response()->json(['success' => false, 'errors' => $validator->errors()->all()], 400);
            }
            $data = $validator->validated();
            $parameter_ids = $data['parameter_ids'];
            $status_id = $data['status_id'];
            foreach (explode(",",$parameter_ids) as $id) {
                $parameter = Parameter::find($id);
                $parameterUpdate = ['status_id' => $status_id,'is_priority' => 1,'description' => $data['description']];
                $parameter->update($parameterUpdate);

                $sentToEWS = updateParameterEWS($parameter->ews_code, $parameterUpdate+['updated_by' => Auth::user()->email])['success'] ?? false;	
                if($sentToEWS){
                    Log::error("Failed to sent to EWS: v1/parameter/$parameter->ews_code - ".$data['description']);
                }
            }
            return response()->json(['success' => true,'message' => 'Status updated successfully!'], 200);
        }catch(Exception $e){
            return response()->json(["success" => false,"message"=>$e->getMessage()],500);
        }
    }

    public function datatable(Request $request, $stackId){
        $parameterModel = Parameter::with(['stack:id,code','status:id,name'])->where(['stack_id' => $stackId]);
        return DataTables::eloquent($parameterModel)->toJson();
    }

    public function select2(Request $request){
        try{
            $where = "1=1";
            $q = $request->q;
            if($q) $where.= " and (sispek_code like '%$q%' or name like '%$q%')";
            $parameters = Parameter::select(['id','parameter_id','sispek_code','name','stack_id'])->with('stack:id,code')->whereRaw($where)->limit(50)->get();
            return response()->json($parameters);
        }catch(Exception $e){
            return response()->json(["error"=>$e->getMessage()],500);
        }
    }

    public function sentToEWS($parameterCode, $data){
        try{
            $data['updated_by'] = Auth::user()->email;
            $request = Http::withHeaders([
                'Authorization' => "Bearer ".getTokenEWS(),
                'Content-Type' => 'application/json'
            ])->post(env('EWS_URL').'/api/v1/parameter/'.$parameterCode, $data)->object();
            if($request->success){
                return true;
            }
            if($request->message) Log::error($request->message);
            return false;
        }catch(Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }
}
