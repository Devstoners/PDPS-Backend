<?php
namespace App\Repositories;

use App\Models\User;
use App\Models\Member;
use App\Models\MemberDivision;
use App\Models\MemberParty;
use App\Models\MemberPosition;
use App\Models\MembersMemberPosition;
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

    public function createMember(array $data) {
        // Check if user with the same email already exists
        $existingUser = User::where('email', $data['email'])->first();
        if ($existingUser) {
            throw new \Exception('Email is already in use', 400);
        }

        // Create user
        $user = User::create([
            'email' => $data['email'],
        ]);
        $user->assignRole('member');

        // Create member
        $member = new Member();
        $member->user_id = $user->id;
        $member->name_en = $data['nameEn'];
        $member->name_si = $data['nameSi'];
        $member->name_ta = $data['nameTa'];
        $member->tel = $data['tel'];
        $member->member_divisions_id = $data['division'];
        $member->member_parties_id = $data['party'];
        //        $member->image = $data['image'];
        //        $member->gender = $data['gender'];
        //        $member->nic = $data['nic'];
        //        $member->address = $data['address'];
        //        $member->is_married = $data['is_married'];
        //        $member->position = $data['position'];
        $member->save();

        // Attach positions to the member
        $positionIds = [];
        foreach ($data['position'] as $positionData) {
            $positionIds[] = $positionData['id'];
        }
        $member->positions()->sync($positionIds);


        $response = [
            'user' => $user,
            'member' => $member,
        ];
        return response($response, 201);

    }



}


