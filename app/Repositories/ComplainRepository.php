<?php

namespace App\Repositories;

use App\Models\Complain;
use App\Models\ComplainAction;
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

    public function getComplain()
    {
        $complain = Complain::select('id', 'created_at', 'cname', 'tele', 'complain', 'img1', 'img2', 'img3')
        ->with('complainAction:id,complain_id,action')
        ->get();

        $response = [
            "AllComplains" => $complain,
        ];

        return response($response, 200);
    }

    public function addAction($request) {

        $complainAction = new complainAction();
        $complainAction->complain_id = $request->id;
        $complainAction->action = $request->action;
        $complainAction->save();
        // Return response
        $response = [
            'complainAction' => $complainAction,
        ];
        return response($response, 201);
    }

    public function updateAction($id, $request)
    {
        $existAction = ComplainAction::where('complain_id', $id)->firstOrFail();

        $existAction->update([
            'action' => $request['action'],
        ]);

        return response(['message' => 'Complain action updated successfully.'], 200);
    }

    public function deleteComplain($id)
    {
        $complain = Complain::find($id);

        if ($complain) {
            try{
                DB::beginTransaction();

                $imagePath1 = $complain->img1;
                if($imagePath1!==null){
                    $imagePath1 = str_replace('/storage/', '', $imagePath1);
                    Storage::disk('public')->delete($imagePath1);
                }

                $imagePath2 = $complain->img2;
                if($imagePath2!==null){
                    $imagePath2 = str_replace('/storage/', '', $imagePath2);
                    Storage::disk('public')->delete($imagePath2);
                }

                $imagePath3 = $complain->img3;
                if($imagePath3!==null){
                    $imagePath3 = str_replace('/storage/', '', $imagePath3);
                    Storage::disk('public')->delete($imagePath3);
                }

                $complain->ComplainAction()->detach();
                $complain->delete();
                DB::commit();
                return true;
            }catch(\Exception $e){
                DB::rollBack();
                return false;
            }
        }
        return false;
    }

}


