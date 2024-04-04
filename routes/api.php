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
Route::apiResource('/news', \App\Http\Controllers\NewsController::class);
Route::get('/newsCount',[\App\Http\Controllers\NewsController::class,'count']);
Route::get('/siteNewsView',[\App\Http\Controllers\NewsController::class,'viewSite']);

Route::apiResource('/downloadActs', \App\Http\Controllers\DownloadActsController::class);
Route::apiResource('/downloadReport', \App\Http\Controllers\DownloadCommitteeReportController::class);

Route::apiResource('/gallery', \App\Http\Controllers\GalleryController::class);

Route::apiResource('/project', \App\Http\Controllers\ProjectController::class);

Route::apiResource('/member', \App\Http\Controllers\MemberController::class);
Route::apiResource('/division', \App\Http\Controllers\DivisionController::class);
Route::apiResource('/memberParty', \App\Http\Controllers\MemberPartyController::class);
Route::apiResource('/memberPosition', \App\Http\Controllers\MemberPositionController::class);


Route::apiResource('/officer', \App\Http\Controllers\OfficerController::class);
Route::apiResource('/officerPosition', \App\Http\Controllers\OfficerPositionController::class);
Route::apiResource('/officerSubject', \App\Http\Controllers\OfficerSubjectController::class);
//Route::put('/officerSubject/{officerSubject}', [\App\Http\Controllers\OfficerSubjectController::class, 'update']);

Route::apiResource('/watersup', \App\Http\Controllers\WaterSupplyController::class);
Route::apiResource('/addComplain', \App\Http\Controllers\ComplainController::class);


Route::apiResource('/addTax', \App\Http\Controllers\TaxController::class);
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/activate', [\App\Http\Controllers\AuthController::class, 'activate']);



Route::get('/complaincount',[\App\Http\Controllers\ComplainController::class,'getCount']);



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
