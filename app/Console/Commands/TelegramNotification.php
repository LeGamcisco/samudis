<?php

namespace App\Console\Commands;

use App\Models\AlarmConfiguration;
use App\Models\DisLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:telegram-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Telegram Notification every hours in minutes 30 if system failure detected';

    /**
     * Execute the console command.
     */
    protected $startAt, $endAt;
    public function __construct()
    {
        parent::__construct();
        // Per 1 Jam kebelakang
        $this->startAt = now()->subHour()->format("Y-m-d H:00:00");
        $this->endAt = Carbon::parse($this->startAt)->addHour()->format("Y-m-d H:00:00");
    }
    public function handle()
    {
        try{
            $config = AlarmConfiguration::find(1);
            if($config->enable_telegram == 0){
                return;
            }
            $this->checkError();
            $notSent = $this->getData(3);
            $overanges = $this->getData(1);
            $overangesNotSent = $this->getData(4);
            $zeroValues = $this->getData(2);
            $zeroValuesNotSent = $this->getData(5);
            $body = "*eGateway System Failure {$this->startAt} - ".Carbon::parse($this->endAt)->format("H:i:00")."*\n";
            $line = 0;
            if(count($notSent) > 0){
                $body.="💢 *Not Sent to SISPEK*\n";
                foreach($notSent as $log){
                    $line++;
                    $unit = strip_tags($log?->parameter?->unit?->name);
                    $time = Carbon::parse($log->time_group)->format("H:i");
                    $body.="{$time} | *{$log?->parameter?->name}* ({$unit}) : *{$log->value_correction}* _[Not Sent]_\n";
                }
            }
            if(count($overanges) > 0){
                $body.="📈 *Overange*\n";
                foreach($overanges as $log){
                    $line++;
                    $unit = strip_tags($log?->parameter?->unit?->name);
                    $time = Carbon::parse($log->time_group)->format("H:i");
                    $body.="{$time} | *[{$log?->parameter?->stack?->code}] {$log?->parameter?->name}* ({$unit}) : *{$log->value_correction}* dari {$log->parameter->max_value}\n";
                }
            }
            if(count($overangesNotSent) > 0){
                $body.="📈💢 *Overange & [Not Sent to SISPEK]*\n";
                foreach($overangesNotSent as $log){
                    $line++;
                    $unit = strip_tags($log?->parameter?->unit?->name);
                    $time = Carbon::parse($log->time_group)->format("H:i");
                    $body.="{$time} | *[{$log?->parameter?->stack?->code}] {$log?->parameter?->name}* ({$unit}) : *{$log->value_correction}* dari *{$log->parameter->max_value}*\n";
                }
            }
            if(count($zeroValues) > 0){
                $body.="📉 *Value Under Zero*\n";
                foreach($overanges as $log){
                    $line++;
                    $unit = strip_tags($log?->parameter?->unit?->name);
                    $time = Carbon::parse($log->time_group)->format("H:i");
                    $body.="{$time} | *[{$log?->parameter?->stack?->code}] {$log?->parameter?->name}* ({$unit}) : *{$log->value_correction}*\n";
                }
            }
            if(count($zeroValuesNotSent) > 0){
                $body.="📉💢 *Under Zero & [Not Sent To SISPEK]*\n";
                foreach($zeroValuesNotSent as $log){
                    $line++;
                    $unit = strip_tags($log?->parameter?->unit?->name);
                    $time = Carbon::parse($log->time_group)->format("H:i");
                    $body.="{$log->time_group} | *[{$log?->parameter?->stack?->code}] {$log?->parameter?->name}* ({$unit}) : *{$log->value_correction}*\n";
                }
            }
            if($line > 0){
                if($this->sentTelegram($body)){
                    DisLog::whereRaw("time_group >= '{$this->startAt}' and time_group <= '{$this->endAt}' and data_status_id = '1'")
                        ->update(['notification' => 7]);
                    $this->info("Telegram Notification Sent");
                }
            }else{
                $this->info("Nothing to send");
            }
        }catch(Exception $e){
            Log::error($e->getMessage(),[
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->error($e->getMessage());
        }

    }

    public function sentTelegram($body){
        $config = AlarmConfiguration::find(1);
        if($config->enable_telegram == 0){
            return;
        }
        $chat_id = $config->telegram_chat_id;
        $token = $config->telegram_bot_token;
        $length = strlen($body);
		$maxAccepted = 4000;
		$maxSending = $length > $maxAccepted ? ceil($length / $maxAccepted) : 1;
		$maxSubstr = ceil($length / $maxSending);
        for ($i=1; $i <= $maxSending ; $i++) {
            $init = ($i == 1) ? 0 : $maxSubstr;
            $text = substr($body, $init, ($maxSubstr+1));
            $text = $this->formatText($text);
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage",[
                'chat_id' => $chat_id,
                'text' => $text,
                'parse_mode' => "Markdown"
            ])->object();
            if($response->ok == 1){
                return true;
            }
            return false;
        }
    }
    public function formatText($text){
        $allowed = [
            '-' => '\\-',
            '|' => '\\|',
            '.' => '\\.',
        ];
        foreach ($allowed as $key => $value) {
            $text = str_replace($value, $allowed[$key], $text);
        }
        return $text;
    }

    public function checkError(){
        $disLogs = $this->getData(0);
        foreach ($disLogs as $log) {
            // Check is data overange
            if($log->value_correction > $log?->parameter?->max_value){
                $log->update(['notification' => 1]); //Overange
                if($log->is_sent_sispek == 0){
                    $log->update(['notification' => 4]); // Overange & Not Sent to SISPEK
                }
            }else if($log->value_correction <= 0.000001){
                $log->update(['notification' => 2]); // Under 0 or minus value
                if($log->is_sent_sispek == 0){
                    $log->update(['notification' => 5]); // Under 0 & Not Sent to SISPEK
                }
            }else{
                $log->update(['notification' => 6]); // Data Checked & Normal
            }
        }
    }

    public function getData($notificationFlag=0){
        return DisLog::whereRaw("time_group >= '{$this->startAt}' and time_group <= '{$this->endAt}' and data_status_id = '1' and notification = '{$notificationFlag}'")
            ->with(["parameter:parameter_id,unit_id,stack_id,name,max_value","parameter.unit:id,name","parameter.stack:id,code"])
            ->get(["id","parameter_id","value_correction","notification","is_sent_sispek","time_group"]);

    }
}
