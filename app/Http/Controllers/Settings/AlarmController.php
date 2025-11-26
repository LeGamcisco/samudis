<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AlarmConfiguration;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AlarmController extends Controller
{
    public function index(){
        $alarm = AlarmConfiguration::first();
        if(empty($alarm)){
            Artisan::call("db:seed --class=AlarmSeeder");
            $alarm = AlarmConfiguration::first();
        }
        return view("settings.alarm", compact('alarm'));
    }
    public function update(AlarmConfiguration $alarm, Request $request){
        $validator = Validator::make($request->all(), [
            // Email
            "enable_email" => 'required',
            "sent_from" => 'required',
            "sent_to" => 'required',
            "protocol" => 'required',
            "host" => 'required',
            "smtp_user" => 'required',
            "smtp_pass" => 'required',
            "smtp_port" => 'required',
            "timeout" => 'required|numeric|max:60',
            // Telegram
            "enable_telegram" => 'required',
            "telegram_chat_id" => 'required',
            "telegram_bot_token" => 'required',
        ],[
            "enable_telegram.required" => "Services Telegram is required",
            "sent_from.required" => "Sent From is required",
            "sent_to.required" => "Sent To is required",
            "protocol.required" => "Email Protocol is required",
            "host.required" => "Email Host is required",
            "smtp_user.required" => "SMTP User is required",
            "smtp_pass.required" => "SMTP Pass is required",
            "smtp_passs.required" => "SMTP Port is required",
            "timeout.required" => "Timeout is required",
            "timeout.max" => "Max. Timeout is 60",
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        $data['sent_to'] = trim(str_replace(" ","",$request->sent_to));
        $alarm->update($data);
        $mailConfig = [
            'transport' => $alarm->protocol,
            'host' => $alarm->host,
            'port' => $alarm->smtp_port,
            'encryption' => null,
            'username' => $alarm->smtp_user,
            'password' => $alarm->smtp_pass,
            'timeout' => null
        ];
        Config::set("mail.mailers.smtp", $mailConfig);

        return redirect()->back()->withSuccess("Alarm Configuration Updated Successfully");
    }

    public function testEmail(){
        try{
            $alarm = AlarmConfiguration::find(1);
            $mailConfig = [
                'transport' => $alarm->protocol,
                'host' => $alarm->host,
                'port' => $alarm->smtp_port,
                'encryption' => null,
                'username' => $alarm->smtp_user,
                'password' => $alarm->smtp_pass,
                'timeout' => $alarm->timeout
            ];
            Config::set("mail.mailers.smtp", $mailConfig);
            Mail::html("eGateway Email Testing. This is a test email.",function($msg){
                $alarm = AlarmConfiguration::find(1);
                $users = explode(",",$alarm->sent_to);
                $msg->to($users)
                ->from($alarm->sent_from, "eGateway Notification")
                ->subject("eGateway Testing Email");
            });
            return response()->json(['success' => true, 'message' => "Email was sent!"]);
        }catch(Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()],500);
        }
    }
    public function testTelegram(){
        try{
            $alarm = AlarmConfiguration::find(1);
            $chat_id  = $alarm->telegram_chat_id;
            $token = $alarm->telegram_bot_token;
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage",[
                'chat_id' => $chat_id,
                'text' => "This is a test telegram message",
                'parse_mode' => "Markdown"
            ])->object();
            if($response->ok == 1){
                return response()->json(['success' => true, 'message' => "Telegram was sent!"]);
            }
            return response()->json(['success' => false, 'message' => $response?->message ?? 'Unable to sent telegram'],400);
        }catch(Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()],500);
        }
    }
}
