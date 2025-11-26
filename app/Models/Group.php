<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $table = "a_groups";
    protected $fillable = [
        "name",
        "menu_ids",
        "privilleges",
        "created_by",
        "created_ip",
        "updated_by",
        "updated_ip",
        "deleted_at",
        "deleted_by",
        "deleted_ip",
    ];
}
