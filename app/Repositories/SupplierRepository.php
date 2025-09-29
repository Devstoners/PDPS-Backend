<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SupplierRepository
{
    /**
     * Add a new supplier
     */
    public function addSupplier(Request $request)
    {
        // Create user first
        $user = User::create([
            'email' => $request->email,
            'name' => $request->name_en,
            'status' => $request->status ?? 1, // Default to active
            'type' => 14, // Supplier type
        ]);
        $user->assignRole('supplier');

        // Handle image upload
        $imgPath = null;
        if ($request->hasFile('img') && $request->file('img')->isValid()) {
            $image = $request->file('img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('images/supplier', $imageName, 'public');
            $imgPath = Storage::url($path);
        }

        // Create supplier
        $supplier = Supplier::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'name_en' => $request->name_en,
            'name_si' => $request->name_si,
            'name_ta' => $request->name_ta,
            'image' => $imgPath,
            'tel' => $request->tel,
            'company_name' => $request->company_name,
            'company_reg_no' => $request->company_reg_no,
            'address' => $request->address,
            'supply_category' => $request->supply_category,
            'contact_person' => $request->contact_person,
            'contact_tel' => $request->contact_tel,
            'contact_email' => $request->contact_email,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);

        return response([
            'user' => $user,
            'supplier' => $supplier,
        ], 201);
    }

    /**
     * Get all suppliers
     */
    public function getSuppliers()
    {
        $suppliers = Supplier::with([
            'user' => function ($query) {
                $query->select('id', 'email', 'status');
            }
        ])
            ->select(
                'id', 'title', 'name_en', 'name_si', 'name_ta', 
                'image', 'tel', 'company_name', 'company_reg_no', 
                'address', 'supply_category', 'contact_person', 
                'contact_tel', 'contact_email', 'description', 
                'is_active', 'user_id', 'created_at', 'updated_at'
            )
            ->get();

        $response = [
            "AllSuppliers" => $suppliers,
        ];
        return response($response);
    }

    /**
     * Get supplier by ID
     */
    public function getSupplierById($id)
    {
        $supplier = Supplier::with([
            'user' => function ($query) {
                $query->select('id', 'email', 'status');
            }
        ])->find($id);

        if (!$supplier) {
            return response()->json(['error' => 'Supplier not found'], 404);
        }

        return response()->json(['supplier' => $supplier], 200);
    }

    /**
     * Update supplier
     */
    public function updateSupplier($id, Request $request)
    {
        $supplier = Supplier::findOrFail($id);

        // Handle image upload
        if ($request->hasFile('img')) {
            // Delete existing image
            if ($supplier->image) {
                $imagePath = str_replace('/storage/', '', $supplier->image);
                Storage::disk('public')->delete($imagePath);
            }

            $image = $request->file('img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('images/supplier', $imageName, 'public');
            $imgPath = Storage::url($path);
        } else {
            $imgPath = $supplier->image;
        }

        // Update supplier
        $supplier->update([
            'title' => $request->title,
            'name_en' => $request->name_en,
            'name_si' => $request->name_si,
            'name_ta' => $request->name_ta,
            'image' => $imgPath,
            'tel' => $request->tel,
            'company_name' => $request->company_name,
            'company_reg_no' => $request->company_reg_no,
            'address' => $request->address,
            'supply_category' => $request->supply_category,
            'contact_person' => $request->contact_person,
            'contact_tel' => $request->contact_tel,
            'contact_email' => $request->contact_email,
            'description' => $request->description,
            'is_active' => $request->is_active ?? $supplier->is_active,
        ]);

        // Update user status if provided
        if ($request->has('status')) {
            $user = User::findOrFail($supplier->user_id);
            $user->update(['status' => $request->status]);
        }

        return response(['message' => 'Supplier updated successfully.'], 200);
    }

    /**
     * Delete supplier
     */
    public function deleteSupplier($id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return false;
        }

        try {
            DB::beginTransaction();

            $userId = $supplier->user_id;

            // Delete supplier image
            if ($supplier->image) {
                $imagePath = str_replace('/storage/', '', $supplier->image);
                Storage::disk('public')->delete($imagePath);
            }

            // Delete supplier record
            $supplier->delete();

            // Delete user and related data
            $user = User::find($userId);
            if ($user) {
                $user->tokens()->delete();
                $user->roles()->detach();
                $user->permissions()->detach();
                $user->delete();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Get supplier count
     */
    public function getCount()
    {
        return Supplier::count();
    }

    /**
     * Get suppliers by category
     */
    public function getSuppliersByCategory($category)
    {
        $suppliers = Supplier::where('supply_category', $category)
            ->where('is_active', true)
            ->with(['user' => function ($query) {
                $query->select('id', 'email', 'status');
            }])
            ->get();

        return response()->json(['suppliers' => $suppliers], 200);
    }

    /**
     * Toggle supplier active status
     */
    public function toggleSupplierStatus($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update(['is_active' => !$supplier->is_active]);

        return response([
            'message' => 'Supplier status updated successfully.',
            'is_active' => $supplier->is_active
        ], 200);
    }
}

