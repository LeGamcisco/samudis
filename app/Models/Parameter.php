<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    protected $table = "parameters";
    public $timestamps = false;
    protected $fillable = [
        "stack_id",
        "parameter_id",
        // "web_id",
        // "web_id_post",
        "ews_code",
        "unit_id",
        "status_id",
        // "is_normalized",
        "p_type",
        "sispek_code",
        "name",
        "caption",
        "formula",
        "is_view",
        "is_graph",
        "molecular_mass",
        "max_value",
        "rounding",
        "ip_analyzer",
        "ain",
        "is_priority"
    ];

    public function Stack(){
        return $this->belongsTo(Stack::class);
    }

    public function Unit(){
        return $this->belongsTo(Unit::class);
    }

    public function Status(){
        return $this->belongsTo(Status::class);
    }
}
