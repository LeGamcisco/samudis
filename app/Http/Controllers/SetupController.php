<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SetupController extends Controller
{
    public function index(){
        // check if exist
        $path = base_path()."\\.env";
        $isFileExist = file_exists($path);
        if($isFileExist && $this->checkDBConnection()){
            return redirect()->route("login")->withErrors("Database already exist!");
        }
        return view("setup.index");
    }

    public function createAdmin(){
        return view("setup.admin");
    }

    public function checkDBConnection(){
        try{
            DB::connection()->getPdo();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    public function testConnection(Request $request){
        $data = $this->validate($request, [
            "type" => "required",
            "host" => "required",
            "port" => "required",
            "username" => "required",
            "password" => "nullable",
            "database" => "required",
        ]);
        $config = Config::get("database.connections.{$data['type']}");
        $config["host"] = $data["host"];
        $config["port"] = $data["port"];
        $config["username"] = $data["username"];
        $config["password"] = $data["password"];
        $config["database"] = $data["database"];
        Config::set("database.default",$data['type']);
        if($data["type"] == "mysql"){
            Config::set("database.connections.mysql", $config);
        }else{
            Config::set("database.connections.pgsql", $config);
        }
        try{
            DB::connection()->getPdo();
            return response()->json(['success' => true, 'message' => "Connection OK"]);
        }catch(Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function doSetup(Request $request){
       try{
            $path = base_path()."\\.env";
            $data = $this->validate($request, [
                'type' => 'required',
                'host' => 'required',
                'port' => 'required',
                'username' => 'required',
                'password' => 'required',
                'database' => 'required',
            ]);
            $config = Config::get("database.connections.{$data['type']}");
            $config["host"] = $data["host"];
            $config["port"] = $data["port"];
            $config["username"] = $data["username"];
            $config["password"] = $data["password"];
            $config["database"] = $data["database"];
            $env = file_get_contents("../.env");
            preg_match('/DB_HOST=(.*)/', $env,$regHost);
            preg_match('/DB_CONNECTION=(.*)/', $env,$regCon);
            preg_match('/DB_USERNAME=(.*)/', $env,$regUser);
            preg_match('/DB_DATABASE=(.*)/', $env,$regDb);
            preg_match('/DB_PASSWORD=(.*)/', $env,$regPass);
            preg_match('/DB_PORT=(.*)/', $env,$regPort);
            $content = str_replace(
                ["DB_HOST=$regHost[1]","DB_CONNECTION=$regCon[1]","DB_USERNAME=$regUser[1]","DB_PASSWORD=$regPass[1]","DB_DATABASE=$regDb[1]","DB_PORT=$regPort[1]"],
                ["DB_HOST={$data['host']}","DB_CONNECTION={$data['type']}","DB_USERNAME={$data['username']}","DB_PASSWORD={$data['password']}","DB_DATABASE={$data['database']}","DB_PORT={$data['port']}"], $env
            );
            file_put_contents("../.env", $content);
            try{
                Config::set("database.default",$data['type']);
                if($data["type"] == "mysql"){
                    Config::set("database.connections.mysql", $config);
                }else{
                    Config::set("database.connections.pgsql", $config);
                }
                DB::connection()->getPdo();
                Artisan::call("key:generate");
                Artisan::call("config:clear");
                Artisan::call("migrate --seed");
                return redirect()->route("login")->withSuccess("Setup Success!")->withInput(["email" => "superuser@trusur.com"]);
            }catch(Exception $e){
                dd($config, $e->getMessage());
                return redirect()->back()->withErrors($e->getMessage());
            }
        }catch(Exception $e){
           return redirect()->back()->withErrors($e->getMessage());
       }
    } 
}
