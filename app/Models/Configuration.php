<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;
    protected $table = "configurations";
    protected $fillable = [
        "egateway_code",
        "customer_name",
        "address",
        "city",
        "province",
        "lon",
        "lat",
        "interval_request",
        "interval_sending",
        "interval_retry",
        "interval_das_logs",
        "interval_average",
        "delay_sending",
        "day_backup",
        "manual_backup",
        "main_path",
        "mysql_path",
        "created_by",
        "created_ip",
        "updated_by",
        "updated_ip",
        "deleted_at",
        "deleted_by",
        "deleted_by",
        "deleted_ip",
    ];

}
