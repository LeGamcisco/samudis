<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleStatus extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "schedule_statuses";
    protected $fillable = [
        'user_id',
        'parameter_id',
        'status_id',
        'description',
        'start_at',
        'end_at',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function parameter(){
        return $this->belongsTo(Parameter::class, 'parameter_id','parameter_id');
    }

    public function status(){
        return $this->belongsTo(Status::class, 'status_id','id');
    }
}
