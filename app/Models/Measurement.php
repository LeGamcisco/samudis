<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    use HasFactory;
    protected $table = "measurements";
    protected $fillable = [
        "parameter_id",  
        "data_status_id",  
        "time_group",  
        "measured_at",  
        "value",  
        "value_correction",
        "unit_id",
        "condition_id",
        "is_sent_cloud",  
        "sent_cloud_type",  
        "sent_cloud_by",  
        "sent_cloud_tries",  
        "is_sent_klhk",  
        "sent_klhk_at",  
        "sent_klhk_tries",  
        "created_at",  
        "created_by",  
        "created_ip",  
        "updated_at",  
        "updated_by",  
        "updated_ip",  
        "is_deleted",  
        "deleted_at",  
        "deleted_ip",  
        "deleted_by",  
        "records_total"
    ];

    public function parameter(){
        return $this->belongsTo(Parameter::class);
    }

    public function status(){
        return $this->belongsTo(Status::class,'data_sattus_id',"id");
    }
    
}
