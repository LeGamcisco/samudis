<?php
date_default_timezone_set("Asia/Jakarta");
while(true){
    $data = [];
    for ($parameterId = 1; $parameterId <= 4; $parameterId++) {
        $data['parameter_id'] = $parameterId;
        $data['data'] = $parameterId == 1 ? rand(0,22) : rand(-5,150);
        $data['date_data'] = date("Y-m-d H:i:s");
        sendData($data);
        unset($data);
    }
    sleep(10);
}
function sendData($data){
    try{
        $url = 'http://172.16.10.254/egateway/api/value-logs';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($result, true);
        print_r($response);
        return true;
    }catch(Exception $e){
        print_r($e);
        return false;
    }
}