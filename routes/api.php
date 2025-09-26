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

// Public Route
    // Site routes
Route::get('/siteNewsView', [\App\Http\Controllers\NewsController::class, 'viewSite']);
Route::post('/siteComplainAdd', [\App\Http\Controllers\ComplainController::class, 'store']);


    // Authentication-related routes
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/activate', [\App\Http\Controllers\AuthController::class, 'activate']);

// Protected Routes with Sanctum Middleware
Route::middleware('auth:sanctum')->group(function () {

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('/member', \App\Http\Controllers\MemberController::class);
        Route::apiResource('/division', \App\Http\Controllers\DivisionController::class);
        Route::apiResource('/memberParty', \App\Http\Controllers\MemberPartyController::class);
        Route::apiResource('/memberPosition', \App\Http\Controllers\MemberPositionController::class);

        Route::apiResource('/officer', \App\Http\Controllers\OfficerController::class);
        Route::get('/officerServices', [\App\Http\Controllers\OfficerServiceController::class, 'index']);
        Route::get('/officerLevels', [\App\Http\Controllers\OfficerLevelController::class, 'index']);
        Route::get('/officerGrades/{serviceId}', [\App\Http\Controllers\OfficerGradeController::class, 'getGradesByService']);
        Route::get('/officerPositions/{serviceId}', [\App\Http\Controllers\OfficerPositionController::class, 'getPositionsByService']);
        Route::get('/officerDuties/{positionId}', [\App\Http\Controllers\OfficerSubjectController::class, 'getDutiesByPosition']);
        Route::apiResource('/officerPosition', \App\Http\Controllers\OfficerPositionController::class);
        Route::apiResource('/officerSubject', \App\Http\Controllers\OfficerSubjectController::class);

    });

    // All users routes
    Route::middleware('role:admin|officer|member')->group(function () {
        Route::apiResource('/complains', \App\Http\Controllers\ComplainController::class);
        Route::apiResource('/complainActions', \App\Http\Controllers\ComplainActionController::class);
        Route::get('/newsCount', [\App\Http\Controllers\NewsController::class, 'count']);
        Route::get('/countDownload', [\App\Http\Controllers\DownloadActsController::class, 'count']);
        Route::get('/countGallery', [\App\Http\Controllers\GalleryImageController::class, 'count']);
        Route::get('/countProject', [\App\Http\Controllers\ProjectController::class, 'count']);
        Route::get('/countMember', [\App\Http\Controllers\MemberController::class, 'count']);
        Route::get('/complaincount', [\App\Http\Controllers\ComplainController::class, 'getCount']);
        Route::get('/countOfficer', [\App\Http\Controllers\OfficerController::class, 'count']);
    });

     // Officer and Admin routes
    Route::middleware('role:officer|admin')->group(function () {
        Route::apiResource('/news', \App\Http\Controllers\NewsController::class);
        Route::apiResource('/downloadActs', \App\Http\Controllers\DownloadActsController::class);
        Route::apiResource('/downloadReport', \App\Http\Controllers\DownloadCommitteeReportController::class);
        Route::apiResource('/gallery', \App\Http\Controllers\GalleryController::class);
        
        // Gallery Image Management Routes
        Route::delete('/gallery-images/{id}', [\App\Http\Controllers\GalleryImageController::class, 'destroy']);
        Route::post('/gallery-images/delete-multiple', [\App\Http\Controllers\GalleryImageController::class, 'deleteMultiple']);
        Route::post('/gallery-images/update-order', [\App\Http\Controllers\GalleryImageController::class, 'updateOrder']);
        Route::apiResource('/project', \App\Http\Controllers\ProjectController::class);
        Route::apiResource('/watersup', \App\Http\Controllers\WaterSupplyController::class);
        Route::apiResource('/addComplain', \App\Http\Controllers\ComplainController::class);

        Route::apiResource('/addTax', \App\Http\Controllers\TaxController::class);
    });

    // Get authenticated user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
