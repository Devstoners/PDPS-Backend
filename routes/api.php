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
Route::apiResource('/complains', \App\Http\Controllers\ComplainController::class);
Route::get('/complaincount',[\App\Http\Controllers\ComplainController::class,'getCount']);

Route::apiResource('/news', \App\Http\Controllers\NewsController::class);
Route::get('/newsCount',[\App\Http\Controllers\NewsController::class,'count']);
Route::get('/siteNewsView',[\App\Http\Controllers\NewsController::class,'viewSite']);

Route::apiResource('/downloadActs', \App\Http\Controllers\DownloadActsController::class);
Route::apiResource('/downloadReport', \App\Http\Controllers\DownloadCommitteeReportController::class);
Route::get('/countDownload',[\App\Http\Controllers\DownloadActsController::class,'count']);//Use to count both Acts and Reports

Route::apiResource('/gallery', \App\Http\Controllers\GalleryController::class);
Route::get('/countGallery',[\App\Http\Controllers\GalleryImageController::class,'count']);

Route::apiResource('/project', \App\Http\Controllers\ProjectController::class);
Route::get('/countProject',[\App\Http\Controllers\ProjectController::class,'count']);

Route::apiResource('/member', \App\Http\Controllers\MemberController::class);
Route::get('/countMember',[\App\Http\Controllers\MemberController::class,'count']);
Route::apiResource('/division', \App\Http\Controllers\DivisionController::class);
Route::apiResource('/memberParty', \App\Http\Controllers\MemberPartyController::class);
Route::apiResource('/memberPosition', \App\Http\Controllers\MemberPositionController::class);


Route::apiResource('/officer', \App\Http\Controllers\OfficerController::class);
Route::get('/countOfficer',[\App\Http\Controllers\OfficerController::class,'count']);
Route::get('/officerServices', [\App\Http\Controllers\OfficerServiceController::class,'index']);
Route::get('/officerLevels', [\App\Http\Controllers\OfficerLevelController::class,'index']);
Route::get('/officerGrades/{serviceId}', [\App\Http\Controllers\OfficerGradeController::class, 'getGradesByService']);
Route::get('/officerPositions/{serviceId}', [\App\Http\Controllers\OfficerPositionController::class, 'getPositionsByService']);
Route::get('/officerDuties/{positionId}', [\App\Http\Controllers\OfficerSubjectController::class, 'getDutiesByPosition']);
Route::apiResource('/officerPosition', \App\Http\Controllers\OfficerPositionController::class);
Route::apiResource('/officerSubject', \App\Http\Controllers\OfficerSubjectController::class);
//Route::put('/officerSubject/{officerSubject}', [\App\Http\Controllers\OfficerSubjectController::class, 'update']);

Route::apiResource('/watersup', \App\Http\Controllers\WaterSupplyController::class);
Route::apiResource('/addComplain', \App\Http\Controllers\ComplainController::class);


Route::apiResource('/addTax', \App\Http\Controllers\TaxController::class);
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/activate', [\App\Http\Controllers\AuthController::class, 'activate']);







Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
