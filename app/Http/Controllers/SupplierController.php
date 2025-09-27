<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Repositories\SupplierRepository;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class SupplierController extends Controller
{
    private $repository;

    public function __construct(SupplierRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *     path="/supplier",
     *     tags={"Suppliers"},
     *     summary="Get all suppliers",
     *     description="Retrieve a list of all suppliers",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Suppliers retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index()
    {
        return $this->repository->getSuppliers();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customMessages = [
            'name_en.required' => 'The name in English field is required.',
            'name_si.required' => 'The name in Sinhala field is required.',
            'name_ta.required' => 'The name in Tamil field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'tel.required' => 'The telephone number field is required.',
            'company_name.required' => 'The company name field is required.',
            'address.required' => 'The address field is required.',
            'supply_category.required' => 'The supply category field is required.',
            'img.image' => 'The file must be an image.',
            'img.mimes' => 'The image must be a JPEG, PNG, or JPG file.',
            'img.max' => 'The image must be less than 5MB in size.',
        ];

        $rules = [
            'name_en' => 'required|string|max:255',
            'name_si' => 'required|string|max:255',
            'name_ta' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'tel' => 'required|string|max:20',
            'company_name' => 'required|string|max:255',
            'address' => 'required|string',
            'supply_category' => 'required|string|max:100',
            'title' => 'required|integer|min:1|max:4',
        ];

        // Add image validation if provided
        if ($request->hasFile('img')) {
            $rules['img'] = 'image|mimes:jpeg,png,jpg|max:5120'; // 5MB max
        }

        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            $response = $this->repository->addSupplier($request);
            return response($response, 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->repository->getSupplierById($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $customMessages = [
            'name_en.required' => 'The name in English field is required.',
            'name_si.required' => 'The name in Sinhala field is required.',
            'name_ta.required' => 'The name in Tamil field is required.',
            'tel.required' => 'The telephone number field is required.',
            'company_name.required' => 'The company name field is required.',
            'address.required' => 'The address field is required.',
            'supply_category.required' => 'The supply category field is required.',
            'img.image' => 'The file must be an image.',
            'img.mimes' => 'The image must be a JPEG, PNG, or JPG file.',
            'img.max' => 'The image must be less than 5MB in size.',
        ];

        $rules = [
            'name_en' => 'required|string|max:255',
            'name_si' => 'required|string|max:255',
            'name_ta' => 'required|string|max:255',
            'tel' => 'required|string|max:20',
            'company_name' => 'required|string|max:255',
            'address' => 'required|string',
            'supply_category' => 'required|string|max:100',
            'title' => 'required|integer|min:1|max:4',
        ];

        // Add image validation if provided
        if ($request->hasFile('img')) {
            $rules['img'] = 'image|mimes:jpeg,png,jpg|max:5120'; // 5MB max
        }

        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            $response = $this->repository->updateSupplier($id, $request);
            return response($response, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->repository->deleteSupplier($id);

        if ($result) {
            return response()->json(['message' => 'Supplier deleted successfully.']);
        }
        return response()->json(['message' => 'Supplier not found.'], 404);
    }

    /**
     * Get supplier count
     */
    public function count()
    {
        $count = $this->repository->getCount();
        $response = [
            "count" => $count,
        ];
        return response($response, 200);
    }

    /**
     * Get suppliers by category
     */
    public function getByCategory($category)
    {
        return $this->repository->getSuppliersByCategory($category);
    }

    /**
     * Toggle supplier status
     */
    public function toggleStatus($id)
    {
        return $this->repository->toggleSupplierStatus($id);
    }
}
