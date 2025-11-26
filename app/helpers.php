<?php

use App\Models\Sispek;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

if(!function_exists('getTable')){
    function getTable($like){
        try{
            $tables = [];
            if(config('database.default') == 'pgsql'){
                $dbName = config('database.connections.pgsql.database');
                $results = DB::select("SELECT table_schema,table_name, table_catalog FROM information_schema.tables WHERE table_catalog = '{$dbName}' AND table_type = 'BASE TABLE' AND table_schema = 'public' and table_name like '{$like}%' ORDER BY table_name;");
                foreach ($results as $value) {
                    $tables[] = $value->table_name;
                }
            }else{
                $dbName = config('database.connections.mysql.database');
                $results = DB::select("show tables where Tables_in_{$dbName} like '$like%'");
                $select = "Tables_in_$dbName";
                foreach ($results as $value) {
                    $tables[] = $value->$select;
                }
            }
            return $tables;
        }catch(Exception $e){
            return [];
        }
    }
}
if(!function_exists('roundMinute')){
    function roundMinute($hour, $minutes = '5', $format = "Y-m-d H:i:00") {
        $seconds = strtotime($hour);
        $rounded = round($seconds / ($minutes * 60)) * ($minutes * 60);
        return date($format, $rounded);
     }
}
// SISPEK Tools
if(!function_exists('testPing')){
    function testPing($url,$port=22){
        $url = $url . ':' . $port;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $health = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($health) {
            return ['health' => $health, 'status' => 1];
        } 
        return ['health' => $health, 'status' => 0];
    }
}
if(!function_exists("getToken")){
    function getToken(){
        try{
            $sispek = Sispek::first();
            $sispek_server = $sispek->server;
            $url = $sispek->api_get_token;
            $app_id = $sispek->app_id;
            $app_secret = $sispek->app_secret;
            $response = Http::withHeaders([
                "cache-control" => "no-cache",
                "content-type" => "application/json"
            ])->post($sispek_server.$url, [
                "app_id" => $app_id,
                "app_pwd_hash" => md5($app_id . $app_secret)
            ])->object();
            $token = $response?->token ?? null;
            $token_expired = date("Y-m-d H:i:s", mktime(date("H"), date("i") + 60));
            $sispek->update(["token" => $token, "token_expired" => $token_expired]);
            return is_null($token) ? false : true;
        }catch(Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }
}

if(!function_exists("HumanSize")){
    function HumanSize($Bytes) {
    $Type=array("", "k", "m", "g", "t", "p", "e", "z", "y");
    $Index=0;
    while($Bytes>=1024)
    {
        $Bytes/=1024;
        $Index++;
    }
    return("".round($Bytes,3)." ".$Type[$Index]."b");
    }
}

if(!function_exists("getRoleById")){
    function getRoleById($id){
        switch ($id) {
            case 0:
                return "Superuser";
                break;
            case 1:
                return "Administrator";
                break;
            case 2:
                return "Operator";
                break;
            default:
                return "User";
                break;
        }
    }
}
if(!function_exists('getTokenEws')){
    /**
     * Retrieves the token from the EWS API.
     *
     * @throws Exception when an error occurs during the token retrieval process.
     * @return string|null The token value or null if an error occurs.
     */
    function getTokenEWS(){
        try{
            $baseUrl = env('EWS_URL');
            $companyCode = env('EWS_CODE');
            $client = new Client();
            $request = $client->post("{$baseUrl}/company/get-token", [
                'verify' => false,
                'form_params' => [
                    'code' => $companyCode,
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json',
                ],
            ]);
            $response = json_decode($request->getBody()->getContents(),1);
            return $response['token'];
        }catch(Exception $e){
            Log::error("Get Token EWS Error: ".$e->getMessage());
            return null;
        }
    }
}
if(!function_exists('sentToEWS')){
    /**
     * Sends a request to the EWS API.
     *
     * @param string $path The API endpoint path.
     * @param array $data The data to be sent in the request.
     * @throws Exception If an error occurs during the request.
     * @return mixed The response from the API.
     */
    function sentToEWS($path, $data){
        try{
            $baseUrl  = env('EWS_URL');
            $client = new Client();
            $token = getTokenEWS();
            $params = [
                'verify' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Bearer '. $token ,
                ],
                'form_params' => $data
            ];
            $request = $client->post("{$baseUrl}/{$path}", $params);
            $response = json_decode($request->getBody()->getContents(),1);
            return $response;
        }catch(Exception $e){
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
if(!function_exists('updateParameterEWS')){
    function updateParameterEWS($parameterCode, $data){
        try{
            $baseUrl  = env('EWS_URL');
            $client = new Client();
            $token = getTokenEWS();
            $params = [
                'verify' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Bearer '. $token ,
                ],
                'form_params' => $data
            ];
            $request = $client->patch("{$baseUrl}/v1/parameter/{$parameterCode}", $params);
            $response = json_decode($request->getBody()->getContents(),1);
            return $response;
        }catch(Exception $e){
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
if(!function_exists("getParameterEws")){
    function getParameterEws($parameterCode){
        try{
            $baseUrl  = env('EWS_URL');
            $client = new Client();
            $token = getTokenEWS();
            $params = [
                'verify' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '. $token ,
                ],
            ];
            $request = $client->get("{$baseUrl}/v1/parameter/{$parameterCode}", $params);
            $response = json_decode($request->getBody()->getContents(),1);
            return $response;
        }catch(Exception $e){
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}