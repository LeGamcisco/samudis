<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeasurementLog extends Model
{
    protected $table = "measurement_logs";
    protected $fillable = ["parameter_id","value","voltage","unit_id","is_averaged","is_das_log","corrective","is_direct_plc","xtimestamp"];
    public $timestamps = false;
    public function parameter(){
        return $this->belongsTo(Parameter::class,'parameter_id','parameter_id');
    }

    public function unit(){
        return $this->belongsTo(Unit::class);
    }
}
