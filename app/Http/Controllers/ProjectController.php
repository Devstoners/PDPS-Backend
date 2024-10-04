<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Repositories\ProjectRepository;
class ProjectController extends Controller
{
    private $repository;
    public function __construct(ProjectRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::select('id', 'name_en','name_si','name_ta','description_si','description_en','description_ta','executor_en','executor_si','executor_ta','budget','start_date','finish_date','status')->get();
        $response = [
            "AllProjects" => $projects,
        ];
        return response($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        \Log::info('Data received for update:', $request->all());
        $customMessages = [
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            'descriptionEn.required' => 'The Description English is compulsory',
            'descriptionSi.required' => 'The Description Sinhala is compulsory',
            'descriptionTa.required' => 'The Description Tamil is compulsory',
            'executorEn.required' => 'The Executing Institute in English is compulsory',
            'executorSi.required' => 'The Executing Institute in Sinhala is compulsory',
            'executorTa.required' => 'The Executing Institute in Tamil is compulsory',
            'startDate.required' => 'The Start date is compulsory',
            'endDate.required' => 'The End date is compulsory',
            'budget.required' => 'The Budget is compulsory',
        ];

        $validator = Validator::make($request->all(), [
            'nameEn' => 'required|string',
            'nameSi' => 'required|string',
            'nameTa' => 'required|string',
            'descriptionEn' => 'required|string',
            'descriptionSi' => 'required|string',
            'descriptionTa' => 'required|string',
            'executorEn' => 'required|string',
            'executorSi' => 'required|string',
            'executorTa' => 'required|string',
            'startDate' => 'required|string',
            'endDate' => 'required|string',
            'budget' => 'required|string',
        ], $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            $response = $this->repository->addProject($request);
            return response($response, 201);
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customMessages = [
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            'descriptionEn.required' => 'The Description English is compulsory',
            'descriptionSi.required' => 'The Description Sinhala is compulsory',
            'descriptionTa.required' => 'The Description Tamil is compulsory',
            'executorEn.required' => 'The Executing Institute in English is compulsory',
            'executorSi.required' => 'The Executing Institute in Sinhala is compulsory',
            'executorTa.required' => 'The Executing Institute in Tamil is compulsory',
            'startDate.required' => 'The Start date is compulsory',
            'endDate.required' => 'The End date is compulsory',
            'budget.required' => 'The Budget is compulsory',
        ];

        $validator = Validator::make($request->all(), [
            'nameEn' => 'required|string',
            'nameSi' => 'required|string',
            'nameTa' => 'required|string',
            'descriptionEn' => 'required|string',
            'descriptionSi' => 'required|string',
            'descriptionTa' => 'required|string',
            'executorEn' => 'required|string',
            'executorSi' => 'required|string',
            'executorTa' => 'required|string',
//            'startDate' => 'required|string',
//            'endDate' => 'required|string',
            'budget' => 'required|string',
        ], $customMessages);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $response = $this->repository->updateProject($id, $request);

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $response = $this->repository->deleteProject($id);

        if ($response->status() === 204) {
            return response()->json(['message' => 'Project deleted successfully.'], 200);
        } elseif ($response->status() === 404) {
            return response()->json(['error' => 'Project not found.'], 404);
        } else {
            return response()->json(['error' => 'Error deleting project.'], 500); // Or any other appropriate status code
        }
    }

    public function count()
    {
        $count = $this->repository->getCount();
        $response = [
            "count" => $count,
        ];
        return response($response, 200);
    }
}
