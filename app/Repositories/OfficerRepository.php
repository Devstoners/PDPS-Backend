<?php

namespace App\Repositories;

use App\Models\OfficerSubject;
use App\Models\OfficerPosition;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


class OfficerRepository
{

    public function addPosition($data)
    {
        //return "Repo awa";
        $position = OfficerPosition::create([
            'position' => $data['position'],
        ]);
        // return $position;
        $responce = [
            'OfficerPosition' => $position,
        ];
        return $responce;
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


