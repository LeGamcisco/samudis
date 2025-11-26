<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;
    protected $table = "statuses";
    protected $fillable = [
        'name',
        'created_by',  
        'created_ip',  
        'updated_by',  
        'update_ip',  
        'is_deleted',  
        'deleted_at',  
        'deleted_by',  
        'deleted_ip',  
    ];
}
