<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Parameter;
use App\Models\Stack;
use App\Models\Status;
use App\Models\Unit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ParameterController extends Controller
{
    public function index(){
        $stacks = Stack::get(['id','code']);
        $units = Unit::get(['id','name']);
        $statuses = Status::get(['id','name']);
        $types = ['main','support','o2'];
        return view("master-data.parameter.index", compact('stacks','units','statuses','types'));
    }

    public function show(Parameter $parameter){
        
    }

    public function create(){
        return view("master-data.parameter.create");
    }

    public function store(Request $request){
        try{
            $data = $this->validateInput($request);
            if(!is_array($data)){
                return $data;
            }
            $parameter = Parameter::create($data);
            return response()->json(["success" => true,"message"=>"Parameter was added successfully!", "data" => $parameter]);
        }catch(Exception $e){
            return response()->json(["Unknown Error: ".$e->getMessage()], 400);
        }
    }

    public function update(Parameter $parameter, Request $request){
        try{
            $data = $this->validateInput($request);
            if(!is_array($data)){
                return $data;
            }
            $parameter->update($data);
            return response()->json(["success" => true,"message"=>"Parameter was update successfully!", "data" => $parameter]);
        }catch(Exception $e){
            return response()->json(["Unknown Error: ".$e->getMessage()], 400);
        }
    }

    public function destroy(Parameter $parameter){
        try{
            $parameter->delete();
            return response()->json(["success" => true,"message"=>"Parameter was deleted successfully!"]);
        }catch(Exception $e){
            return response()->json(["Unknown Error: ".$e->getMessage()], 400);
        }
    }

    public function datatable(Request $request){
        $whereRaw = "1=1";
        $filterFields = ["stack_id","status_id","p_type"];
        foreach ($filterFields as $filter) {
            if($request->filled("$filter")){
                $whereRaw.=" and $filter = '{$request->$filter}'";
            }
        }
        $parameterModel = Parameter::with(['stack:id,code','status:id,name','unit:id,name'])->whereRaw($whereRaw);
        return DataTables::eloquent($parameterModel)->toJson();
    }

    public function validateInput(Request $request){
        $validator = Validator::make($request->all(),[
            "parameter_id" => "required",
            "stack_id" => "required",
            "name" => "required",
            "sispek_code" => "nullable",
            "unit_id" => "required",
            "p_type" => "required",
            "status_id" => "required",
            "rounding" => "required|min:0",
            "max_value" => "nullable",
            "ain" => "nullable",
            "ip_analyzer" => "nullable",
            "enable_notification" => "required",
            "formula" => "nullable",
            "web_id" => "nullable",
            "web_id_post" => "nullable",
            "is_normalized" => "nullable",
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all(), 400);
        }
        $data = $validator->validated();
        return $data;
    }
}
