<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConfigurationController extends Controller
{
    public function index(){
        $dbName = env("DB_DATABASE");
        $dbUsage = DB::select("select pg_size_pretty(pg_database_size('{$dbName}')) as size")[0]?->size;
        $config = Configuration::first();
        return view('settings.configuration', compact('config','dbUsage'));
    }
    public function update(Configuration $configuration, Request $request){
        $validator = Validator::make($request->all(), [
            'egateway_code' => 'required',
            'customer_name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'province' => 'required',
            'lat' => 'required',
            'lon' => 'required',
            'interval_das_logs' => 'required',
            'interval_average' => 'required',
            'day_backup' => 'required',
            'manual_backup' => 'required',
            'main_path' => 'required',
            'mysql_path' => 'required',
        ],[
            'egateway_code.required' => 'eGateway Code is required',
            'customer_code.required' => 'Company Name is required',
            'city.required' => 'City is required',
            'province.required' => 'Province is required',
            'lat.required' => 'Latitude is required',
            'lon.required' => 'Longitude is required',
            'interval_das_logs.required' => 'Interval Raw Data is required',
            'interval_average.required' => 'Interval Measurement Averaging is required',
            'manual_backup.required' => 'Manual Backup is required',
            'day_backup.required' => 'Day Backup',
            'main_path.required' => 'Main Path is required',
            'mysql_path.required' => 'DBMS Path is required',
        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        $configuration->update($data);
        return redirect()->route('settings.configuration.index')->withSuccess("Configuration updated successfully");
    }
}
