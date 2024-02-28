<?php

namespace App\Http\Controllers;


use App\Models\OfficerSubject;
use Illuminate\Http\Request;
use App\Repositories\OfficerRepository;
use Illuminate\Support\Facades\Validator;

class OfficerSubjectController extends Controller
{

    private $repository;
    public function __construct(OfficerRepository $repository)
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
        $subject = OfficerSubject::select('id', 'subject_en','subject_si','subject_ta')->get();
        $response = [
            "AllSubjects" => $subject,
        ];
        return response($response, 200);
//        return OfficerSubject::all();
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
        $customMessages = [
            'subjectEn' => 'The Subject English is compulsory',
            'subjectSi' => 'The Subject Sinhala is compulsory',
            'subjectTa' => 'The Subject Tamil is compulsory',
        ];

        $validator = Validator::make($request->all(),[
            'subjectEn' => 'required',
            'subjectSi' => 'required',
            'subjectTa' => 'required',
        ], $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        }else{
            $response = $this->repository->addSubject($request);
            return response($response, 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OfficerSubject  $officerSubject
     * @return \Illuminate\Http\Response
     */
    public function show(OfficerSubject $officerSubject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OfficerSubject  $officerSubject
     * @return \Illuminate\Http\Response
     */
    public function edit(OfficerSubject $officerSubject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OfficerSubject  $officerSubject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customMessages = [
            'subjectEn' => 'The Subject English is compulsory',
            'subjectSi' => 'The Subject Sinhala is compulsory',
            'subjectTa' => 'The Subject Tamil is compulsory',
        ];

        $validator = Validator::make($request->all(),[
            'subjectEn' => 'required',
            'subjectSi' => 'required',
            'subjectTa' => 'required',
        ], $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        }else{
            $response = $this->repository->updateSubject($id, $request);
            return response($response, 200);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OfficerSubject  $officerSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = $this->repository->deleteSubject($id);

        if ($response->status() === 204) {
            return response()->json(['message' => 'Subject deleted successfully.'], 200);
        } elseif ($response->status() === 404) {
            return response()->json(['error' => 'Subject not found.'], 404);
        } else {
            return response()->json(['error' => 'Error deleting subject.'], 500); // Or any other appropriate status code
        }
    }
}
