<?php

namespace App\Repositories;

use App\Models\OfficerSubject;
use App\Models\OfficerPosition;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


class OfficerRepository
{


    //-----------------Position--------------------------------------------------------------------
    public function addPosition($data)
    {
        $position = OfficerPosition::create([
            'position_en' => $data['positionEn'],
            'position_si' => $data['positionSi'],
            'position_ta' => $data['positionTa'],
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return response([
            'position' => $position
        ], 201);
    }

    public function updatePosition($id, $data)
    {
        $position = OfficerPosition::find($id);
        $position->update([
            'position_en' => $data['positionEn'],
            'position_si' => $data['positionSi'],
            'position_ta' => $data['positionTa'],
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return response(['message' => 'Position updated successfully.'], 200);
    }
    public function deletePosition($id)
    {
        $position = OfficerPosition::find($id);

        if ($position) {
            $position->delete();
            return response()->noContent(); // Send 204 upon successful delete
        }
        return response()->noContent()->setStatusCode(404); // Send 404 if position not found
    }



    //-----------------Subject--------------------------------------------------------------------
    public function addSubject($data)
    {
        $subject = OfficerSubject::create([
            'subject_en' => $data['subjectEn'],
            'subject_si' => $data['subjectSi'],
            'subject_ta' => $data['subjectTa'],
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return response([
            'Subject' => $subject
        ], 201);
    }

    public function updateSubject($id, $data)
    {
        $subject = OfficerSubject::find($id);
        $subject->update([
            'subject_en' => $data['subjectEn'],
            'subject_si' => $data['subjectSi'],
            'subject_ta' => $data['subjectTa'],
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return response(['message' => 'Subject updated successfully.'], 200);
    }
    public function deleteSubject($id)
    {
        $subject = OfficerSubject::find($id);

        if ($subject) {
            $subject->delete();
            return response()->noContent(); // Send 204 upon successful delete
        }
        return response()->noContent()->setStatusCode(404); // Send 404 if subject not found
    }

}


