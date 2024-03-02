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
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return response([
            'acts' => $acts
        ], 200);

    }

    public function updateActs($id, $data)
    {
        $acts = DownloadActs::find($id);
        $acts->update([
            'name_en' => $data['nameEn'],
            'name_si' => $data['nameSi'],
            'name_ta' => $data['nameTa'],
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return response(['message' => 'Acts updated successfully.'], 200);
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


