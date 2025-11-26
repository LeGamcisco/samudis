<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes for Py Serve DEMO
Route::prefix('streams')->group(function () {
    Route::get("{webId}/interpolated");
    Route::get("{webId}/recorded");
    Route::get("{webId}/value", function($webId){
        return response()->json([
            "Timestamp" => now()->subMinute()->format("Y-m-d\TH:i:00\Z"),
            "Value" => rand(-2,100) / rand(1,10),
            "UnitsAbbreviation" => "",
            "Good" => true,
            "Questionable" => true,
            "Substituted" => true,
        ]); 
    });
    Route::post("{webId}/value", function($webId){
        return response()->json([
            "success" => true,
            "message" => "Accepted!",
            "webId" => $webId
        ],202);
    });
});