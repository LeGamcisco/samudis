<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stack extends Model
{
    use HasFactory;
    protected $table = "stacks";
    protected $fillable = [
        "code",
        "sispek_code",
        "ews_code",
        "height",
        "diameter",
        "flow",
        "lat",
        "lon",
        "oxygen_reference",
        "created_by",
        "created_ip",
        "updated_ip",
        "is_deleted",
        "deleted_at",
        "deleted_by",
        "deleted_ip",
    ];

    public function Parameters(){
        return $this->hasMany(Parameter::class, 'stack_id', 'id');
    }
}
