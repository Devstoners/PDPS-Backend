<?php

namespace App\Http\Controllers;

use App\Models\DownloadActs;
use App\Repositories\DownloadRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DownloadActsController extends Controller
{
    private $repository;
    public function __construct(DownloadRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return DownloadActs::select('id','number','issue_date','name_en','name_si','name_ta','file_path_en','file_path_si','file_path_ta')->get();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $customMessages = [
            'number.required' => 'The Act Number is compulsory',
            'issueDate.required' => 'The Issue Date is compulsory',
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            'actFileEn.mimetypes' => 'The file (English) must be a PDF file',
            'actFileEn.max' => 'The file (English) may not be greater than 25 MB',
            'actFileSi.mimetypes' => 'The file (Sinhala) must be a PDF file',
            'actFileSi.max' => 'The file (Sinhala) may not be greater than 25 MB',
            'actFileTa.mimetypes' => 'The file (Tamil) must be a PDF file',
            'actFileTa.max' => 'The file (Tamil) may not be greater than 25 MB',
        ];

        $validator = Validator::make($request->all(), [
            'number' => 'required|string',
            'issueDate' => 'required|date',
            'nameEn' => 'required|string',
            'nameSi' => 'required|string',
            'nameTa' => 'required|string',
        ], $customMessages);

        if ($request->hasFile('actFileEn')) {
            $validator->sometimes('actFileEn', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('actFileEn');
            });
        }
        if ($request->hasFile('actFileSi')) {
            $validator->sometimes('actFileSi', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('actFileSi');
            });
        }
        if ($request->hasFile('actFileTa')) {
            $validator->sometimes('actFileTa', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('actFileTa');
            });
        }

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        }

        return $this->repository->addAct($request);
    }

    public function show($id)
    {
        $act = DownloadActs::select('id','number','issue_date','name_en','name_si','name_ta','file_path_en','file_path_si','file_path_ta')->find($id);
        if (!$act) {
            return response()->json(['error' => 'Act not found.'], 404);
        }
        return response()->json($act, 200);
    }

    public function edit(DownloadActs $downloadActs)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $customMessages = [
            'number.required' => 'The Act Number is compulsory',
            'issueDate.required' => 'The Issue Date is compulsory',
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            'actFileEn.mimetypes' => 'The file (English) must be a PDF file',
            'actFileEn.max' => 'The file (English) may not be greater than 25 MB',
            'actFileSi.mimetypes' => 'The file (Sinhala) must be a PDF file',
            'actFileSi.max' => 'The file (Sinhala) may not be greater than 25 MB',
            'actFileTa.mimetypes' => 'The file (Tamil) must be a PDF file',
            'actFileTa.max' => 'The file (Tamil) may not be greater than 25 MB',
        ];

        $validator = Validator::make($request->all(), [
            'number' => 'required|string',
            'issueDate' => 'required|date',
            'nameEn' => 'required|string',
            'nameSi' => 'required|string',
            'nameTa' => 'required|string',
        ], $customMessages);

        if ($request->hasFile('actFileEn')) {
            $validator->sometimes('actFileEn', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('actFileEn');
            });
        }
        if ($request->hasFile('actFileSi')) {
            $validator->sometimes('actFileSi', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('actFileSi');
            });
        }
        if ($request->hasFile('actFileTa')) {
            $validator->sometimes('actFileTa', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('actFileTa');
            });
        }

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        }

        return $this->repository->updateAct($id, $request);
    }

    public function destroy($id)
    {
        $response = $this->repository->deleteAct($id);
        if ($response->status() === 204) {
            return response()->json(['message' => 'Act deleted successfully.'], 200);
        } elseif ($response->status() === 404) {
            return response()->json(['error' => 'Act not found.'], 404);
        }
        return response()->json(['error' => 'Error deleting act.'], 500);
    }
}
