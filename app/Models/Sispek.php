<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sispek extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'sispek';
    protected $fillable = [
        "server",
        "app_id",
        "app_secret",
        "api_get_token",
        "api_get_kode_cerobong",
        "api_get_parameter",
        "api_post_data",
        "api_response_kode_cerobong",
        "api_response_kode_parameter",
        "token",
        "token_expired",
    ];
}
