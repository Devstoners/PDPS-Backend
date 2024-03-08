<?php

namespace App\Repositories;

use App\Models\DownloadCommitteeReport;
use App\Models\DownloadActs;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
class DownloadRepository
{

    //-----------------Acts--------------------------------------------------------------------
    public function addActs($request)
    {
        $filePathEn = null;
        if ($request->hasFile('actFileEn')) {
            $file = $request->file('actFileEn');
            $fileName = time() . '_en.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('acts', $fileName, 'public');
            $filePathEn = str_replace('storage/', '', $path);
        }

        $filePathSi = null;
        if ($request->hasFile('actFileSi')) {
            $file = $request->file('actFileSi');
            $fileName = time() . '_si.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('acts', $fileName, 'public');
            $filePathSi = str_replace('storage/', '', $path);
        }

        $filePathTa = null;
        if ($request->hasFile('actFileTa')) {
            $file = $request->file('actFileTa');
            $fileName = time() . '_ta.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('acts', $fileName, 'public');
            $filePathTa = str_replace('storage/', '', $path);
        }

        $acts = DownloadActs::create([
            'number' => $request['actNumber'],
            'issue_date' => $request['actDate'],
            'name_en' => $request['nameEn'],
            'name_si' => $request['nameSi'],
            'name_ta' => $request['nameTa'],
            'file_path_en' => $filePathEn,
            'file_path_si' => $filePathSi,
            'file_path_ta' => $filePathTa,
            'created_at' => now(),
        ]);
        return response([
            'acts' => $acts
        ], 200);

    }

    public function updateActs($id, $data)
    {
        $existActs = DownloadActs::find($id);


         if ($data->hasFile('actFileEn')) {
            if ($existActs->file_path_en) {
                Storage::disk('public')->delete($existActs->file_path_en);
            }
            $file = $data->file('actFileEn');
            $fileName = time() . '_en.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('acts', $fileName, 'public');
            $filePathEn = str_replace('storage/', '', $path);
        }else{
             $filePathEn = $existActs->file_path_en;
         }


        if ($data->hasFile('actFileSi')) {
            if ($existActs->file_path_si) {
                Storage::disk('public')->delete($existActs->file_path_si);
            }
            $file = $data->file('actFileSi');
            $fileName = time() . '_si.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('acts', $fileName, 'public');
            $filePathSi = str_replace('storage/', '', $path);
        }else{
            $filePathSi = $existActs->file_path_si;
        }

        if ($data->hasFile('actFileTa')) {
            if ($existActs->file_path_ta) {
                Storage::disk('public')->delete($existActs->file_path_ta);
            }
            $file = $data->file('actFileTa');
            $fileName = time() . '_ta.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('acts', $fileName, 'public');
            $filePathTa = str_replace('storage/', '', $path);
        }else{
            $filePathTa = $existActs->file_path_ta;
        }

        $acts = DownloadActs::find($id);
        if($acts) {
            $acts->update([
                'number' => $data['actNumber'],
                'issue_date' => $data['actDate'],
                'name_en' => $data['nameEn'],
                'name_si' => $data['nameSi'],
                'name_ta' => $data['nameTa'],
                'file_path_en' => $filePathEn,
                'file_path_si' => $filePathSi,
                'file_path_ta' => $filePathTa,
                'updated_at' => now(),
            ]);
            return response(['message' => 'Acts updm,nsadn,aated successfully.'], 200);
        }else{
            \Log::info('id eka ne:');
        }
    }
    public function deleteActs($id)
    {
        $acts = DownloadActs::find($id);

        if ($acts) {
            if ($acts->file_path_en) {
                Storage::disk('public')->delete($acts->file_path_en);
            }
            if ($acts->file_path_si) {
                Storage::disk('public')->delete($acts->file_path_si);
            }
            if ($acts->file_path_ta) {
                Storage::disk('public')->delete($acts->file_path_ta);
            }

            $acts->delete();

            return response()->noContent(); // Send 204 upon successful delete
        }

        return response()->noContent()->setStatusCode(404); // Send 404 if act not found

    }


}


