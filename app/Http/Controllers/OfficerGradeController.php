<?php

namespace App\Http\Controllers;

use App\Models\OfficerGrade;
use App\Http\Requests\StoreOfficerGradeRequest;
use App\Http\Requests\UpdateOfficerGradeRequest;

class OfficerGradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // public function getGradesByService($serviceId) {
    //     $grades = Grade::where('service_id', $serviceId)->get();
    //     return response()->json($grades);
    // }

    public function getGradesByService($serviceId) {
        if (!$serviceId) {
            return response()->json(['error' => 'Service ID is required'], 400);
        }
        $grades = OfficerGrade::where('officer_services_id', $serviceId)->get();
        return response()->json($grades);
    }

}
