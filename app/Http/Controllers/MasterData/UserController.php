<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index($groupId = null){
        $groups = Group::get();
        $group = Group::find($groupId);
        return view("master-data.user",compact("groups","group","groupId"));
    }

    public function update(User $user, Request $request){
        try{
            $data = $request->validate([
                "name" => "required|regex:/^[a-zA-Z\s]+$/u",
                "email" => "required|unique:a_users,email,$user->id",
                "phone" => "required|numeric",
                "group_id" => "required|numeric",
                "password" => "nullable|confirmed|min:6",
            ],[
                "name.required" => "Fullname is required!",
                "name.regex" => "Invalid format name, must be alphabet!",
                "email.required" => "Email is required!",
                "email.unique" => "Email is already registed!",
                "phone.required" => "Phone is required!",
                "phone.numeric" => "Invalid format phone, must be numeric!",
                "group_id.required" => "Role must be selected!",
                "password.confirmed" => "Wrong Password Confirmation!",
                "password.min" => "Password at least 6 character",
            ]);
            if(Auth::user()->group_id > 0 && $user->group_id == 0){
                return response()->json([
                    'success' => false,
                    'message' => 'You dont have permission to update this user!'
                ],403);

            }
            $user->update($data);
            return response()->json(["success" => true, "message" => "User was updated successfully!", "data" => $user],200);
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ],500);
        }
        
    }

    public function store(Request $request){
        try{
            $data = $this->validate($request,[
                "name" => "required|regex:/^[a-zA-Z\s]+$/u",
                "group_id" => "required|numeric",
                "email" => "required|unique:a_users,email",
                "phone" => "required|numeric",
                "password" => "required|confirmed|min:6",   
            ],[
                "name.required" => "Name is required!",
                "name.regex" => "Invalid format name, must be alphabet!",
                "group_id.required" => "Please select role!",
                "email.required" => "Email is required!", 
                "email.email" => "Invalid format email!", 
                "phone.numeric" => "Invalid format phone, must be numeric!", 
                "password.required" => "Password is required!", 
                "password.confirmed" => "Password confirmation is wrong!",
                "password.min" => "Password at least 6 character", 
            ]);
            $data['password'] = Hash::make($data['password']);
            User::create($data);
            return response()->json(['success' => true, 'message' => 'User was created successfully!']);
        }catch(Exception $e){
            Log::error("Create user error".$e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()],500);
        }

    }

    public function destroy(User $user){
        try{
            if($user->group_id < 2){
                return response()->json(['success' => false,'message' => 'You dont have permission to delete this user!'], 403);
            }
            $user->delete();
            return response()->json(['success' => true,'message' => 'User was deleted successfully!'], 200);
        }catch(Exception $e){
            return response()->json(['success' => true,'message' => 'Unable to delete!'], 500);
        }
    }

    public function datatable(Request $request){
        $groupId = $request->group_id;
        if($groupId == null){
            $whereRaw = "1=1";
        }else{
            $whereRaw = "group_id = $groupId";
        }
        $userModel = User::with(["group:id,name"])->whereRaw($whereRaw);
        return DataTables::eloquent($userModel)->toJson();
    }
}
