<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Parameter;
use App\Models\Reference;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parameters = Parameter::with(["stack:id,code"])->where("is_has_reference",1)->get();
        return view('master-data.reference.index', compact("parameters"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $data = $request->validate([
                'parameter_id' => 'required',
                'range_start' => 'required',
                'range_end' => 'required',
                'formula' => 'required',
            ]);

            Reference::create($data);

            return response()->json(["success" => true,"message"=>"Reference was added successfully!"]);
        }catch(Exception $e){
            return response()->json(["Error: ".$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Reference $reference)
    {
        return response()->json(["success" => true,"data" => $reference]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reference $reference)
    {
        try{
            $data = $request->validate([
                'parameter_id' => 'required',
                'range_start' => 'required',
                'range_end' => 'required',
                'formula' => 'required',
            ]);
            $reference->update($data);
            return response()->json(["success" => true,"message" => "Reference was updated successfully!"]);
        }catch(Exception $e){
            return response()->json(["Error: ".$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reference $reference)
    {
        try{
            if(!in_array(Auth::user()->role_id, [0,1])){
                return response()->json(["success" => false,"message" => "You dont have permission to delete this reference!"], 403);
            }
            $reference->delete();
            return response()->json(["success" => true,"message" => "Reference was deleted successfully!"]);
        }catch(Exception $e){
            return response()->json(["Error: ".$e->getMessage()], 500);
        }
    }

    public function datatable(Request $request){
        try{
            $data = Reference::with(['parameter:id,stack_id,name','parameter.stack:id,code']);
            return DataTables::eloquent($data)->toJson();
        }catch(Exception $e){
            return response()->json(["Error: ".$e->getMessage()], 500);
        }
    }
}
