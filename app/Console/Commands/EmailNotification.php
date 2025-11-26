<?php

namespace App\Console\Commands;

use App\Models\AlarmConfiguration;
use App\Models\DisLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:email-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Email Notification every hours in minutes 30 if system failure detected';

    /**
     * Execute the console command.
     */
    protected $startAt, $endAt;
    public function __construct()
    {
        parent::__construct();
        $this->startAt = now()->subHour()->format("Y-m-d H:00:00");
        $this->endAt = Carbon::parse($this->startAt)->addHour()->format("Y-m-d H:00:00");

    }
    public function handle()
    {
        try{
            $config = AlarmConfiguration::find(1);
            if($config->enable_email == 0){
                return;
            }
            $this->checkError();
            $notSent = $this->getData(3);
            $overanges = $this->getData(1);
            $overangesNotSent = $this->getData(4);
            $zeroValues = $this->getData(2);
            $zeroValuesNotSent = $this->getData(5);
            $body = "Dear User, Below is the log of system failure:</br>";
            $body.= "<b>Samu System Failure {$this->startAt} - ".Carbon::parse($this->endAt)->format("H:i:00")."</b></br>";
            $line = 0;
            if(count($notSent) > 0){
                $body.="💢 <b>Not Sent to SISPEK</b></br>";
                foreach($notSent as $log){
                    $line++;
                    $unit = strip_tags($log?->parameter?->unit?->name);
                    $time = Carbon::parse($log->time_group)->format("H:i");
                    $body.="{$time} | <b>{$log?->parameter?->name}</b> ({$unit}) : <b>{$log->value_correction}</b> [Not Sent]</br>";
                }
            }
            if(count($overanges) > 0){
                $body.="📈 <b>Overange</b></br>";
                foreach($overanges as $log){
                    $line++;
                    $unit = strip_tags($log?->parameter?->unit?->name);
                    $time = Carbon::parse($log->time_group)->format("H:i");
                    $body.="{$time} | <b>{$log?->parameter?->name}</b> ({$unit}) : <b>{$log->value_correction}</b> dari {$log->parameter->max_value}</br>";
                }
            }
            if(count($overangesNotSent) > 0){
                $body.="📈💢 <b>Overange & [Not Sent to SISPEK]</b></br>";
                foreach($overangesNotSent as $log){
                    $line++;
                    $unit = strip_tags($log?->parameter?->unit?->name);
                    $time = Carbon::parse($log->time_group)->format("H:i");
                    $body.="{$time} | <b>{$log?->parameter?->name}</b> ({$unit}) : </b>{$log->value_correction}</b> dari <b>{$log->parameter->max_value}</b></br>";
                }
            }
            if(count($zeroValues) > 0){
                $body.="📉 <b>Value Under Zero</b></br>";
                foreach($overanges as $log){
                    $line++;
                    $unit = strip_tags($log?->parameter?->unit?->name);
                    $time = Carbon::parse($log->time_group)->format("H:i");
                    $body.="{$time} | <b>{$log?->parameter?->name}</b> ({$unit}) : <b>{$log->value_correction}</b></br>";
                }
            }
            if(count($zeroValuesNotSent) > 0){
                $body.="📉💢 <b>Under Zero & [Not Sent To SISPEK]</b></br>";
                foreach($zeroValuesNotSent as $log){
                    $line++;
                    $unit = strip_tags($log?->parameter?->unit?->name);
                    $time = Carbon::parse($log->time_group)->format("H:i");
                    $body.="{$log->time_group} | <b>{$log?->parameter?->name}</b> ({$unit}) : <b>{$log->value_correction}</b></br>";
                }
            }
            if($line > 0){
                if($this->sentEmail($body)){
                    DisLog::whereRaw("time_group >= '{$this->startAt}' and time_group <= '{$this->endAt}' and data_status_id = '1'")
                        ->update(['notification_email' => 7]);
                    $this->info("Email Notification Sent");
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

    public function sentEmail($body){
        $config = AlarmConfiguration::find(1);
        $mailConfig = [
            'transport' => $config->protocol,
            'host' => $config->host,
            'port' => $config->smtp_port,
            'encryption' => null,
            'username' => $config->smtp_user,
            'password' => $config->smtp_pass,
            'timeout' => $config->timeout
        ];
        Config::set("mail.mailers.smtp", $mailConfig);
        if($config->enable_email == 0){
            return;
        }
        $title = "Samu System Failure {$this->startAt} - ".Carbon::parse($this->endAt)->format("H:i:00");
        $users = explode(",",$config->sent_to);
        try{
            Mail::html($body,function($msg) use($users,$title,$config){
                $msg->to($users)
                ->from($config->sent_from, "Samu Notification")
                ->subject($title);
            });
            return true;
        }catch(Exception $e){
            Log::error($e->getMessage(),[
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return false;
        }
    }
    public function checkError(){
        $disLogs = $this->getData(0);
        foreach ($disLogs as $log) {
            // Check is data overange
            if($log->value_correction > $log?->parameter?->max_value){
                $log->update(['notification_email' => 1]); //Overange
                if($log->is_sent_sispek == 0){
                    $log->update(['notification_email' => 4]); // Overange & Not Sent to SISPEK
                }
            }else if($log->value_correction <= 0.000001){
                $log->update(['notification_email' => 2]); // Under 0 or minus value
                if($log->is_sent_sispek == 0){
                    $log->update(['notification_email' => 5]); // Under 0 & Not Sent to SISPEK
                }
            }else{
                $log->update(['notification_email' => 6]); // Data Checked & Normal
            }
        }
    }

    public function getData($notificationFlag=0){
        return DisLog::whereRaw("time_group >= '{$this->startAt}' and time_group <= '{$this->endAt}' and data_status_id = '1' and notification_email = '{$notificationFlag}'")
            ->with(["parameter:parameter_id,name,unit_id,max_value","parameter.unit:id,name"])
            ->get(["id","parameter_id","value_correction","notification_email","is_sent_sispek","time_group"]);

    }
}
