<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisLog extends Model
{
    use HasFactory;
    protected $table = "dis_logs";
    protected $fillable = [
        "parameter_id",
        "data_status_id",
        "unit_id",
        "validation_id",
        "condition_id",
        "time_group",
        "measured_at",
        "value",
        "value_correction",
        "avg_time_group",
        "is_averaged",
        "is_sent_cloud",
        "is_sent_sispek",
        "sent_sispek_at",
        "sent_sispek_by",
        "sent_cloud_at",
        "sent_cloud_by",
        "notification",
        "notification_email",
    ];
    public $timestamps = false;

    public function Parameter(){
        return $this->belongsTo(Parameter::class,'parameter_id','parameter_id');
    }
    public function Status(){
        return $this->belongsTo(Status::class);
    }
    public function Unit(){
        return $this->belongsTo(Unit::class);
    }
}
