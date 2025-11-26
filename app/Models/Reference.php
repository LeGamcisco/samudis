<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    use HasFactory;

    protected $table = 'references';

    protected $fillable = [
        'parameter_id',
        'range_start',
        'range_end',
        'formula',
    ];
    

    public function parameter(){
        return $this->belongsTo(Parameter::class);
    }
}
