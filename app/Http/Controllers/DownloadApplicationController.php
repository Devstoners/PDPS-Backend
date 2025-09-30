<?php

namespace App\Http\Controllers;

use App\Models\DownloadApplication;
use App\Repositories\DownloadRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DownloadApplicationController extends Controller
{
    private $repository;
    public function __construct(DownloadRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return DownloadApplication::select('id','application_year','application_month','name_en','name_si','name_ta','file_path_en','file_path_si','file_path_ta')->get();
    }

    public function show($id)
    {
        $application = DownloadApplication::select('id','application_year','application_month','name_en','name_si','name_ta','file_path_en','file_path_si','file_path_ta')->find($id);
        if (!$application) {
            return response()->json(['error' => 'Application not found.'], 404);
        }
        return response()->json($application, 200);
    }

    public function store(Request $request)
    {
        $customMessages = [
            'applicationYear.required' => 'The Application Year is compulsory',
            'applicationMonth.required' => 'The Application Month is compulsory',
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            'applicationFileEn.mimetypes' => 'The file (English) must be a PDF file',
            'applicationFileEn.max' => 'The file (English) may not be greater than 25 MB',
            'applicationFileSi.mimetypes' => 'The file (Sinhala) must be a PDF file',
            'applicationFileSi.max' => 'The file (Sinhala) may not be greater than 25 MB',
            'applicationFileTa.mimetypes' => 'The file (Tamil) must be a PDF file',
            'applicationFileTa.max' => 'The file (Tamil) may not be greater than 25 MB',
        ];

        $validator = Validator::make($request->all(), [
            'applicationYear' => 'required|string',
            'applicationMonth' => 'required|string',
            'nameEn' => 'required|string',
            'nameSi' => 'required|string',
            'nameTa' => 'required|string',
        ], $customMessages);

        if ($request->hasFile('applicationFileEn')) {
            $validator->sometimes('applicationFileEn', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('applicationFileEn');
            });
        }
        if ($request->hasFile('applicationFileSi')) {
            $validator->sometimes('applicationFileSi', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('applicationFileSi');
            });
        }
        if ($request->hasFile('applicationFileTa')) {
            $validator->sometimes('applicationFileTa', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('applicationFileTa');
            });
        }

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        }

        return $this->repository->addApplication($request);
    }

    public function update(Request $request, $id)
    {
        $customMessages = [
            'applicationYear.required' => 'The application year is compulsory',
            'applicationMonth.required' => 'The application month is compulsory',
            'nameEn.required' => 'The name in English is compulsory',
            'nameSi.required' => 'The name in Sinhala is compulsory',
            'applicationFile.required' => 'The file is compulsory',
            'applicationFile.mimes' => 'The file must be a PDF document',
            'applicationFile.max' => 'The file may not be greater than 10 MB',
        ];

        $baseRules = [
            'applicationYear' => 'required',
            'applicationMonth' => 'required',
            'nameEn' => 'required',
            'nameSi' => 'required',
        ];

        if ($request->hasFile('applicationFile')) {
            $baseRules['applicationFile'] = 'required|mimes:pdf|max:10240';
        }

        $validator = Validator::make($request->all(), $baseRules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        }

        return $this->repository->updateApplication($id, $request);
    }

    public function destroy($id)
    {
        $response = $this->repository->deleteApplication($id);
        if ($response->status() === 204) {
            return response()->json(['message' => 'Application deleted successfully.'], 200);
        } elseif ($response->status() === 404) {
            return response()->json(['error' => 'Application not found.'], 404);
        }
        return response()->json(['error' => 'Error deleting application.'], 500);
    }
}


