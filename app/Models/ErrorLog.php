<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    use HasFactory;
    protected $table = "error_logs";
    protected $fillable = [
        "error_id",
        "type_id",
        "parameter_id",
        "status_id",
        "measured",
        "corrective",
        "message",
        "is_sent_ews",
        "sent_ews_at",
        "time_group",
    ];
    public function parameter(){
        return $this->belongsTo(Parameter::class,"parameter_id");
    }
}
