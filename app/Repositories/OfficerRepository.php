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
        ], 200);
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
            return true;
        }
        return false;
    }


    public function addSubject($data)
    {
        //return "Repo awa";
        $subject = OfficerSubject::create([
            'subject' => $data['subject'],
        ]);
        // return $subject;
        $responce = [
            'OfficerSubject' => $subject,
        ];
        return $responce;
    }

}


