<?php

use App\Http\Controllers\About\VersionController;
use App\Http\Controllers\Api\ValueLogsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Database\BackupController;
use App\Http\Controllers\MasterData\ParameterController;
use App\Http\Controllers\MasterData\ReferenceController;
use App\Http\Controllers\MasterData\UserController;
use App\Http\Controllers\Settings\AlarmController;
use App\Http\Controllers\Settings\ConfigurationController;
use App\Http\Controllers\Settings\ParameterStatusController;
use App\Http\Controllers\MasterData\StackController;
use App\Http\Controllers\Measurement\AnalyticController as MeasurementAnalyticController;
use App\Http\Controllers\Measurement\MeasurementController;
use App\Http\Controllers\RawData\AnalyticController;
use App\Http\Controllers\RawData\DisLogController;
use App\Http\Controllers\ScheduleStatusController;
use App\Http\Controllers\Settings\SispekController;
use App\Http\Controllers\SetupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Route::get("/setup",[SetupController::class,"index"])->name("setup.index");
// Route::post("/setup/do-setup",[SetupController::class,"doSetup"])->name("setup.doSetup");
// Route::post("/setup/test-connection",[SetupController::class,"testConnection"])->name("setup.test-connection");
// Route::get("/setup/do-setup",[SetupController::class,"doSetup"])->name("setup.doSetup");

Route::get("/login",[LoginController::class,'login'])->name("login");
Route::post("/login",[LoginController::class,'doLogin'])->name("login.doLogin");
Route::delete("/logout",[LoginController::class,'logout'])->name("logout");


// Not require to login first
Route::get("/value-logs/{stackId}",[DashboardController::class,"getValueLogs"]);
Route::get("/realtime-line-chart/{parameterId}",[DashboardController::class,"getLineChart"]);
Route::get("/datatable/value-sent/{stackId}",[DashboardController::class,"getValueSent"])->name("datatable.value-sent");

// API DIS
Route::group(["prefix" => "api","name" => "api."], function(){
    Route::post("/value-logs",[ValueLogsController::class,"store"]);
});

// Must login to access below URL
Route::group(["middleware" => "auth"], function(){
   // Dashboard
    Route::get('/',[DashboardController::class,'index'])->name("dashboard");
    Route::get("/realtime/{stackId}",[DashboardController::class,"index"])->name("dashboard.realtime");
    Route::prefix('measurement')->name("measurement.")->group(function(){
        Route::get('/',[MeasurementController::class,'index'])->name('index');
        Route::get('datatable',[MeasurementController::class,'datatable'])->name('datatable');
        Route::get('export',[MeasurementController::class,'export'])->name('export');
        Route::get('export-klhk',[MeasurementController::class,'exportGovernment'])->name('export-klhk');
        Route::get('export-monthly',[MeasurementController::class,'exportMonthly'])->name('export-monthly');

        Route::get('analytic',[MeasurementAnalyticController::class,'index'])->name('analytic.index');
        Route::get('analytic/data/{parameterId}',[MeasurementAnalyticController::class,'getData']);

    });
    Route::prefix('dis-logs')->name("dis-logs.")->group(function(){
        Route::get('/',[DisLogController::class,'index'])->name('index');
        Route::get('datatable',[DisLogController::class,'datatable'])->name('datatable');
        Route::get('export',[DisLogController::class,'export'])->name('export');
        Route::post('unsent',[DisLogController::class,'unsent'])->name('unsent');

        Route::get('analytic',[AnalyticController::class,'index'])->name('analytic');
        Route::get('analytic/data/{parameterId}',[AnalyticController::class,'getData']);

    });
    Route::prefix('settings')->name('settings.')->group(function(){
        Route::get('schedule-status/datatable',[ScheduleStatusController::class,'datatable'])->name('schedule-status.datatable');
        Route::resource('schedule-status',ScheduleStatusController::class)->except(["edit","create"]);
        // Setting Parameter Status
        Route::get('parameter/select2',[ParameterStatusController::class,'select2'])->name("parameter.select2");
        Route::get('parameter/datatable/{stackId}',[ParameterStatusController::class,'datatable'])->name("parameter.datatable");
        Route::get('parameter/{stackId}',[ParameterStatusController::class,'index']);
        Route::get('parameter',[ParameterStatusController::class,'index'])->name("parameter.index");
        Route::patch('parameter',[ParameterStatusController::class,'update'])->name("parameter.update");
    });
    Route::group(['middleware' => 'auth.admin'], function(){
        Route::prefix('settings')->name("settings.")->group(function(){
            // Configuration
            Route::post("alarm/test-email",[AlarmController::class,'testEmail'])->name("alarm.test-email");
            Route::post("alarm/test-telegram",[AlarmController::class,'testTelegram'])->name("alarm.test-telegram");
            Route::resource('alarm',AlarmController::class)->except(['show','edit','create','store','destroy','delete']);
            Route::resource('configuration',ConfigurationController::class)->except(['show','edit','create','store','destroy','delete']);
            Route::resource("sispek",SispekController::class)->except("show","edit","create","store","destroy","delete");
            Route::get("sispek/test-ping",[SispekController::class,'testPing'])->name("sispek.test-ping");
            Route::post("sispek/get-stack-code",[SispekController::class,'getStackCode'])->name("sispek.stack-code");
            Route::post("sispek/get-parameters",[SispekController::class,'getParameters'])->name("sispek.parameter-code");

        });
        // Backup & Restore
        Route::prefix('database')->name("database.")->group(function(){
            Route::get('backup',[BackupController::class,'index'])->name("backup.index");
            Route::post('backup',[BackupController::class,'backup'])->name("backup.backup");
            Route::get('backup/datatable',[BackupController::class,'datatable'])->name("backup.datatable");
            Route::get('backup/download/{file}',[BackupController::class,'download'])->name("backup.download");
            Route::delete('backup/{file}',[BackupController::class,'destroy'])->name("backup.destroy");
            Route::post('restore',[BackupController::class,'restore'])->name("backup.restore");
        });
        // Master Data
        Route::prefix('master')->name("master.")->group(function(){
            // User
            Route::resource('user',UserController::class)->except(["show","edit","create","delete"]);
            Route::get('user/datatable',[UserController::class,'datatable'])->name("user.datatable");
            Route::get('user/{groupId}',[UserController::class,'index']);
            // Stack
            Route::get('stack/datatable',[StackController::class,'datatable'])->name("stack.datatable");
            Route::resource('stack',StackController::class);
            // Parameter
            Route::get('parameter/datatable',[ParameterController::class,'datatable'])->name("parameter.datatable");
            Route::resource('parameter',ParameterController::class);
            // Reference
            Route::get('reference/datatable',[ReferenceController::class,'datatable'])->name("reference.datatable");
            Route::resource('reference',ReferenceController::class);
            // Select2 Master Data
        });
    });
    Route::get("master/stack/parameters/{stack}",[StackController::class,'getParameters'])->name("master.stack.parameters");
});
