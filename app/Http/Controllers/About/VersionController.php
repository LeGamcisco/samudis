<?php

namespace App\Http\Controllers\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    public function index(){
        return view("about.version");
    }
    public function documentation(){
        return view("about.documentation");
    }
}
