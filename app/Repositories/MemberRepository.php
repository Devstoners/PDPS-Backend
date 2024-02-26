<?php
namespace App\Repositories;

use App\Models\User;
use App\Models\Member;
use App\Models\MemberDivision;
use App\Models\MemberParty;
use App\Models\MemberPosition;
use App\Models\MembersMemberPosition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class MemberRepository{
//-----------------Division--------------------------------------------------------------------
       public function addDivision($data)
    {
        $division = MemberDivision::create([
            'division_en' => $data['divisionEn'],
            'division_si' => $data['divisionSi'],
            'division_ta' => $data['divisionTa'],
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return response([
            'division' => $division
        ], 200);
    }

    public function deleteDivision($id)
    {
        $division = MemberDivision::find($id);

        if ($division) {
            $division->delete();
            return true;
        }
        return false;
    }

    public function updateDivision($id, $data)
    {
        $division = MemberDivision::find($id);
        $division->update([
            'division_en' => $data['divisionEn'],
            'division_si' => $data['divisionSi'],
            'division_ta' => $data['divisionTa'],
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return response(['message' => 'Division updated successfully.'], 200);
    }

//-----------------Party--------------------------------------------------------------------
    public function addParty($data)
    {
        $party = MemberParty::create([
            'party_en' => $data['partyEn'],
            'party_si' => $data['partySi'],
            'party_ta' => $data['partyTa'],
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return response([
            'party' => $party
        ], 200);
    }

    public function updateParty($id, $data)
    {
        $party = MemberParty::find($id);
        $party->update([
            'party_en' => $data['partyEn'],
            'party_si' => $data['partySi'],
            'party_ta' => $data['partyTa'],
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return response(['message' => 'Party updated successfully.'], 200);
    }
    public function deleteParty($id)
    {
        $party = MemberParty::find($id);

        if ($party) {
            $party->delete();
            return true;
        }
        return false;
    }

    //-----------------Position--------------------------------------------------------------------
    public function addPosition($data)
    {
        $position = MemberPosition::create([
            'position_en' => $data['positionEn'],
            'position_si' => $data['positionSi'],
            'position_ta' => $data['positionTa'],
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return response([
            'position' => $position
        ], 200);
    }

    public function updatePosition($id, $data)
    {
        $position = MemberPosition::find($id);
        $position->update([
            'position_en' => $data['positionEn'],
            'position_si' => $data['positionSi'],
            'position_ta' => $data['positionTa'],
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return response(['message' => 'Position updated successfully.'], 200);
    }
    public function deletePosition($id)
    {
        $position = MemberPosition::find($id);

        if ($position) {
            $position->delete();
            return true;
        }
        return false;
    }

    //-----------------Member--------------------------------------------------------------------

    public function getMembers()
    {
        $members = Member::with([
            'memberDivision' => function ($query) {
                $query->select('id', 'division_en');
            },
            'memberParty' => function ($query) {
                $query->select('id', 'party_en');
            },
            'memberPositions' => function ($query) {
                $query->select('member_positions.id', 'position_en');
            },
            'user' => function ($query) {
                $query->select('id', 'email', 'status');
            }
        ])
            ->select('members.id', 'name_en', 'name_si','name_ta', 'image', 'tel', 'member_divisions_id', 'member_parties_id', 'user_id')
            ->get();

        $response = [
            "AllMembers" => $members,
        ];
        return response($response);
//        return response()->json($response);
    }


    public function createMember(Request $request) {
        // Check if user with the same email already exists
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            throw new \Exception('Email is already in use', 400);
        }

        // Validate request data
        $request->validate([
            'nameEn' => 'required|string',
            'nameSi' => 'required|string',
            'nameTa' => 'required|string',
            'email' => 'required|email',
            'division' => 'required|array',
            'party' => 'required|array',
            'position' => 'required|array',
            'tel' => 'required|string',
            'img' => 'required|image|mimes:jpeg|max:5048',
        ]);

        // Create user
        $user = User::create([
            'email' => $request->email,
        ]);
        $user->assignRole('member');

        // Handle image upload
        $imgPath = null;
        if($request->hasFile('img') && $request->file('img')->isValid()) {
            $image = $request->file('img');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $path = $image->storeAs('images', $imageName, 'public');
            $imgPath = Storage::url($path);
        }
        dd($imgPath);

        // Create member
        $member = new Member();
        $member->user_id = $user->id;
        $member->name_en = $request->nameEn;
        $member->name_si = $request->nameSi;
        $member->name_ta = $request->nameTa;
        $member->tel = $request->tel;
        $member->member_divisions_id = $request->division['value'];
        $member->member_parties_id = $request->party['value'];
//        $member->image = $imgPath;
        $member->save();

        // Handle positions
        $positionIds = [];
        foreach ($request->position as $positionData) {
            $positionIds[] = $positionData['value'];
        }
        $member->memberPositions()->sync($positionIds);

        // Return response
        $response = [
            'user' => $user,
            'member' => $member,
        ];
        return response($response, 201);
    }


    public function deleteMember($id)
    {
        $member = Member::find($id);

        if ($member) {
            $userId = $member->user_id;
//            $userId = Member::where('id', $id)->value('user_id');
            try{
                DB::beginTransaction();
                $member->delete();
                $user = User::find($userId);
                if($user){
                    $user->tokens()->delete();
                    $user->roles()->detach();
                    $user->permissions()->detach();
                    $user->delete();
                }
                DB::commit();
                return true;
            }catch(\Exception $e){
                DB::rollBack();
                return false;
            }
        }
        return false;
    }




}


