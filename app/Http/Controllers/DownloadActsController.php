<?php

namespace App\Http\Controllers;

use App\Models\DownloadActs;
use App\Repositories\DownloadRepository;
use App\Http\Requests\StoreDownloadActsRequest;
use App\Http\Requests\UpdateDownloadActsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DownloadActsController extends Controller
{

    private $repository;
    public function __construct(DownloadRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $acts = DownloadActs::select('id', 'number','issue_date','name_en','name_si','name_ta','file_path_en','file_path_si','file_path_ta')->get();
        $response = [
            "AllActs" => $acts,
        ];
        return response($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
//        \Log::info('Data received for update:', $request->all());
        $customMessages = [
            'actNumber.required' => 'The Act Number is compulsory',
            'actDate.required' => 'The Act Date is compulsory',
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
            'actNumber' => 'required|string',
            'actDate' => 'required|string',
            'nameEn' => 'required|string',
            'nameSi' => 'required|string',
            'nameTa' => 'required|string',
        ], $customMessages);

        // Validate English file if it's uploaded
        if ($request->hasFile('actFileEn')) {
            $validator->sometimes('actFileEn', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('actFileEn');
            });
        }

        // Validate Sinhala file if it's uploaded
        if ($request->hasFile('actFileSi')) {
            $validator->sometimes('actFileSi', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('actFileSi');
            });
        }

        // Validate Tamil file if it's uploaded
        if ($request->hasFile('actFileTa')) {
            $validator->sometimes('actFileTa', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('actFileTa');
            });
        }

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            $response = $this->repository->addActs($request);
            return response($response, 201);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(DownloadActs $downloadActs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DownloadActs $downloadActs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
//        \Log::info('Data received for update acts:', $request->all());

        $customMessages = [
            'actNumber.required' => 'The Act Number is compulsory',
            'actDate.required' => 'The Act Date is compulsory',
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
            'actNumber' => 'required|string',
            'actDate' => 'required|string',
            'nameEn' => 'required|string',
            'nameSi' => 'required|string',
            'nameTa' => 'required|string',
        ], $customMessages);

        // Validate English file if it's uploaded
        if ($request->hasFile('actFileEn')) {
            $validator->sometimes('actFileEn', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('actFileEn');
            });
        }

        // Validate Sinhala file if it's uploaded
        if ($request->hasFile('actFileSi')) {
            $validator->sometimes('actFileSi', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('actFileSi');
            });
        }

        // Validate Tamil file if it's uploaded
        if ($request->hasFile('actFileTa')) {
            $validator->sometimes('actFileTa', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('actFileTa');
            });
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $response = $this->repository->updateActs($id, $request);

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $response = $this->repository->deleteActs($id);

        if ($response->status() === 204) {
            return response()->json(['message' => 'Act deleted successfully.'], 200);
        } elseif ($response->status() === 404) {
            return response()->json(['error' => 'Act not found.'], 404);
        } else {
            return response()->json(['error' => 'Error deleting act.'], 500); // Or any other appropriate status code
        }
    }
}
