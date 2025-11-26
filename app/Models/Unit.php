<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    protected $table = "units";
    protected $fillable = [
      "name",
      "created_by",  
      "created_ip",  
      "update_by",  
      "update_ip",  
      "is_deleted",  
      "deleted_at",  
      "deleted_by",  
      "deleted_ip",  
    ];
}
