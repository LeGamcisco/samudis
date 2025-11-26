<?php

namespace App\Http\Controllers;

use App\Models\ScheduleStatus;
use App\Models\Status;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ScheduleStatusController extends Controller
{
    public function index(){
        $statuses = Status::get(['id','name']);
        return view('schedule-status.index',compact('statuses'));
    }

    public function show(ScheduleStatus $schedule_status){
        $schedule_status->load([
            "parameter:id,parameter_id,stack_id,name",
            "parameter.stack:id,code",
            "status:id,name",
            "user:id,name",
        ]);
        return response()->json($schedule_status);
    }

    public function store(Request $request){
        try{
            $data = $this->validate($request, [
                'parameter_id' => 'required|array',
                'status_id' => 'required',
                'start_at' => 'required',
                'end_at' => 'required',
                'description' => 'nullable',
            ]); 
            $data['user_id'] = Auth::user()->id;
            if(now()->isAfter($data['start_at'])){
                return response()->json(['success' => false,'message' => 'Cant add schedule status. Start at must be after now!']);
            }
            if(Carbon::parse($data['end_at'])->isBefore(Carbon::parse($data['start_at']))){
                return response()->json(['success' => false,'message' => 'Cant add schedule status. End at must be after Start at!']);
            }
            foreach ($data['parameter_id'] as $parameterId) {
                $data['parameter_id'] = $parameterId;
                ScheduleStatus::create($data);
            }
            return response()->json(['success' => true,'message' => 'Schedule status added successfully!'], 200);

        }catch(Exception $e){
            return response()->json(['success' => false,'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, ScheduleStatus $schedule_status){
        try{
            $data = $this->validate($request, [
                'status_id' => 'required',
                'start_at' => 'required',
                'end_at' => 'required',
                'description' => 'nullable',
            ]); 
            $data['user_id'] = Auth::user()->id;
            if(now()->isAfter($data['start_at'])){
                return response()->json(['success' => false,'message' => 'Cant add schedule status. Start at must be after now!']);
            }
            if(Carbon::parse($data['end_at'])->isBefore(Carbon::parse($data['start_at']))){
                return response()->json(['success' => false,'message' => 'Cant add schedule status. End at must be after Start at!']);
            }
            $schedule_status->update($data);
            return response()->json(['success' => true,'message' => 'Schedule status updated successfully!'], 200);
        }catch(Exception $e){
            return response()->json(['success' => false,'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(ScheduleStatus $schedule_status){
        try{
            if($schedule_status->user_id != Auth::user()->id){
                return response()->json(['success' => false,'message' => 'You dont have permission to delete this schedule status!'], 403);
            }
            $schedule_status->delete();
            return response()->json(['success' => true,'message' => 'Schedule status deleted successfully!'], 200);
        }catch(Exception $e){
            return response()->json(['success' => false,'message' => $e->getMessage()], 500);
        }
        
    }

    public function datatable(){
       try{
            $schedule = ScheduleStatus::with([
                'parameter:parameter_id,stack_id,name',
                'parameter.stack:id,code',
                'status:id,name',
                'user:id,name'
            ]);
            return DataTables::of($schedule)->toJson();
       }catch(Exception $e){
           return $e->getMessage();
       }
    }
}
