<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Stack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class StackController extends Controller
{
    public function index(){
        return view("master-data.stack.index");
    }
    public function show(Stack $stack){
        return view("master-data.stack.show", compact("stack"));
    }

    public function store(Request $request){
        $data = $this->validateInput($request);
        if(!is_array($data)){
            return $data;
        }
        Stack::create($data);
        return response()->json(["success" => true,"message" => "Stack was added successfully!"],200);

    }
    public function update(Stack $stack, Request $request){
        $data = $this->validateInput($request);
        if(!is_array($data)){
            return $data;
        }
        $stack->update($data);
        return response()->json(["success" => true,"message" => "Stack was updated successfully!"],200);
    }
    
    public function destroy(Stack $stack){
        $stack->delete();
        return response()->json(["success" => true,"message" => "Stack $stack->code was deleted successfully!"],200);
        
    }

    public function datatable(){
        $stackModel = Stack::query();
        return DataTables::eloquent($stackModel)->toJson();
    }

    public function getParameters(Stack $stack){
        return response()->json(['parameters' => $stack->parameters]);
    }

    public function validateInput($request){
        $validator = Validator::make($request->all(),[
            "code" => "required",
            "sispek_code" => "nullable",
            "oxygen_reference" => "required",
            "height" => "required",
            "diameter" => "required",
            "flow" => "required",
            "lat" => "required",
            "lon" => "required",
        ],[
            "code.required" => "Code is required!",
            "oxygen_reference.required" => "Oxygen Reference is required!",
            "height.required" => "Height is required!",
            "diameter.required" => "Diameter is required!",
            "flow.required" => "Flow is required!",
            "lat.required" => "Latitude is required!",
            "lon.required" => "Longitude is required!",
        ]);
        if($validator->fails()){
            return response()->json(["errors"=>$validator->errors()->all()],400);
        }
        $data = $validator->validated();
        return $data;
    }
}
