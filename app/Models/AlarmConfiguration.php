<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlarmConfiguration extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "alarm_configurations";
    protected $fillable = [
        "enable_email",
        "enable_telegram",
        "sent_from",
        "sent_to",
        "protocol",
        "host",
        "smtp_user",
        "smtp_pass",
        "smtp_port",
        "timeout",
        "telegram_chat_id",
        "telegram_bot_token",
    ];
}
