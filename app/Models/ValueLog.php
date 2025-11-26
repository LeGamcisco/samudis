<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValueLog extends Model
{
    use HasFactory;
    protected $table = "value_logs";
    protected $fillable = ["parameter_id","measured","corrective"];
    public $timestamps = false;
    
    public function Parameter(){
        return $this->belongsTo(Parameter::class,'parameter_id','parameter_id');
    }
}
