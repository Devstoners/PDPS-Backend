<?php

namespace App\Repositories;

use App\Models\Complain;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


class ComplainRepository
{


    public function addComplain($data)
    {
        //  return $data;
        $complain = Complain::create([
            'cname' => $data['cname'],
            'tele' => $data['tele'],
            'complain' => $data['complain'],
             'complain_date' => $data['complain_date'],
//            'img1'=> $data['img1'],
//            'img2'=> $data['img2'],
//            'img3'=> $data['img3'],
        ]);
        // return $complain;
        $responce = [
            'Complain' => $complain,
        ];
        return $responce;
    }


}


