<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Sispek;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class SispekController extends Controller
{
    public function index(){
        $sispek = Sispek::first();
        if(!empty($sispek)){
            return view("settings.sispek", compact('sispek'));
        }
        Artisan::call("db:seed --class=SispekSeeder");
        return redirect()->to(route("settings.sispek.index"));
    }

    public function update(Sispek $sispek, Request $request){
        try{
            $data = $this->validate($request, [
                'server' => 'required',
                'app_id' => 'required',
                'app_secret' => 'required',
                'api_get_token' => 'required',
                'api_get_kode_cerobong' => 'required',
                'api_get_parameter' => 'required',
                'api_post_data' => 'required',
                'token' => 'nullable',
                'token_epxired' => 'nullable',
            ]);
            $sispek->update($data);
            return redirect()->back()->withSuccess('SISPEK Configuration was updated successfully!');
        }catch(Exception $e){
            return redirect()->back()->withError($e->getMessage());
        }
    }
    public function testPing(){
        try{
            $data = testPing("https://ditppu.menlhk.go.id/sispekv2/api/v2/token");
            return response()->json([
                "success" => true,
                "data" => $data
            ]);
        }catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
    
    public function getStackCode(){
        try{
            if(!getToken()){
                return response()->json([
                    "success" => false,
                    "message" => "Invalid token!"
                ],403);
            }
            $sispek = Sispek::first();
            $token = $sispek->token;
            $sispek_server = $sispek->server;
            $url = $sispek->api_get_kode_cerobong;
            $data = json_encode(["Key" => "Bearer " . $token]);
            $response = Http::withHeaders([
                "Authorization" => "Bearer " . $token,
                "Api-Key" => "Bearer " . $token,
                "key" => "Bearer " . $token,
                "cache-control" => "no-cache",
                "content-type" => "application/json"
            ])->post($sispek_server . $url, $data)->object();
            $data = $response;
            return response()->json([
                "success" => true,
                "data" => $data
            ]);
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ],500);
        }
    }
    public function getParameters(Request $request){
        try{
            if(!getToken()){
                return response()->json([
                    "success" => false,
                    "message" => "Invalid token!"
                ],403);
            }
            $request->validate([
                "code_cerobong" => "required"
            ]);
            $code_cerobong = $request->code_cerobong;
            $sispek = Sispek::first();
            $sispek_server = $sispek->server;
            $url = $sispek->api_get_parameter;
            $token = $sispek->token;
            $response = Http::withHeaders([
                "Authorization" => "Bearer " . $token,
                "Api-Key" => "Bearer " . $token,
                "key" => "Bearer " . $token,
                "cache-control" => "no-cache",
                "content-type" => "application/json"
            ])->post($sispek_server . $url, [
                "cerobong_kode" => $code_cerobong
            ])->object();
            $data = $response;
            return response()->json([
                "success" => true,
                "data" => $data
            ]);
        }catch(Exception $e){
            return response()->json([
                "success" => false,
                "errors" => [$e->getMessage()]
            ], 400);
        }
    }
}
