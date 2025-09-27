<?php

namespace App\Repositories;
use App\Models\Project;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProjectRepository
{
    public function addProject($request)
    {
        $startDate = Carbon::parse($request['startDate'])->toDateString();
        $endDate = Carbon::parse($request['endDate'])->toDateString();

        $project = Project::create([
            'name_en' => $request['nameEn'],
            'name_si' => $request['nameSi'],
            'name_ta' => $request['nameTa'],
            'description_si' => $request['descriptionEn'],
            'description_en' => $request['descriptionSi'],
            'description_ta' => $request['descriptionTa'],
            'executor_si' => $request['executorEn'],
            'executor_en' => $request['executorSi'],
            'executor_ta' => $request['executorTa'],
            'budget' => $request['budget'],
            'status' => $request->input('status'),
            'start_date' => $startDate,
            'finish_date' => $endDate,
        ]);
        return response([
            'acts' => $project
        ], 200);

    }


    public function updateProject($id, $request)
    {
        $startDate = Carbon::parse($request['startDate'])->toDateString();
        $endDate = Carbon::parse($request['endDate'])->toDateString();

        $existProject = Project::findOrFail($id);
        $existProject->update([
            'name_en' => $request->input('nameEn'),
            'name_si' => $request->input('nameSi'),
            'name_ta' => $request->input('nameTa'),
            'description_si' => $request->input('descriptionEn'),
            'description_en' => $request->input('descriptionSi'),
            'description_ta' => $request->input('descriptionTa'),
            'executor_si' => $request->input('executorEn'),
            'executor_en' => $request->input('executorSi'),
            'executor_ta' => $request->input('executorTa'),
            'start_date' => $startDate,
            'finish_date' => $endDate,
            'budget' => $request->input('budget'),
            'status' => $request->input('status'),

        ]);

        return response(['message' => 'Project updated successfully.'], 200);
    }



    public function deleteProject($id)
    {
        $project = Project::find($id);

        if ($project) {
            $project->delete();
            return response()->noContent(); // Send 204 upon successful delete
        }
        return response()->noContent()->setStatusCode(404);
    }

    public function getCount()
    {
        return Project::count();
    }
}


