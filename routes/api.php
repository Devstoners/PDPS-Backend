<?php

use App\Http\Controllers\AuthController;
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
//Me vidiyata (apiResource) anith ewageth hadanna e.g.-officer, officerPosition. ewata crud set kranna
Route::apiResource('/news', \App\Http\Controllers\NewsController::class);
Route::apiResource('/officer', \App\Http\Controllers\OfficerController::class);

Route::apiResource('/watersup', \App\Http\Controllers\WaterSupplyController::class);
Route::apiResource('/addComplain', \App\Http\Controllers\ComplainController::class);


Route::apiResource('/addTax', \App\Http\Controllers\TaxController::class);
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/activate', [\App\Http\Controllers\AuthController::class, 'activate']);
Route::apiResource('/addPosition', \App\Http\Controllers\OfficerPositionController::class);
Route::apiResource('/addSubject', \App\Http\Controllers\OfficerSubjectController::class);


Route::get('/newscount',[\App\Http\Controllers\NewsController::class,'getNewsCount']);
Route::get('/siteNewsView',[\App\Http\Controllers\NewsController::class,'siteNewsView']);
Route::get('/complaincount',[\App\Http\Controllers\ComplainController::class,'getCount']);



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
