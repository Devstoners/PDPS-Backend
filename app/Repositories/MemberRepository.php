<?php
namespace App\Repositories;

use App\Models\User;
use App\Models\Member;
use App\Models\Division;
use App\Models\MemberParty;
use App\Models\MemberPosition;
use App\Models\MembersMemberPosition;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\MemberRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class MemberRepository extends BaseRepository implements MemberRepositoryInterface
{
    public function __construct(Member $model)
    {
        parent::__construct($model);
    }
//-----------------Division--------------------------------------------------------------------
    /**
     * Add division
     */
    public function addDivision(array $data): array
    {
        $division = Division::create([
            'division_en' => $data['divisionEn'],
            'division_si' => $data['divisionSi'],
            'division_ta' => $data['divisionTa'],
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return [
            'division' => $division
        ];
    }

    /**
     * Delete division
     */
    public function deleteDivision(int $id): bool
    {
        $division = Division::find($id);

        if ($division) {
            $division->delete();
            return true;
        }
        return false;
    }

    /**
     * Update division
     */
    public function updateDivision(int $id, array $data): array
    {
        $division = Division::find($id);
        $division->update([
            'division_en' => $data['divisionEn'],
            'division_si' => $data['divisionSi'],
            'division_ta' => $data['divisionTa'],
            'updated_at' => now(),
        ]);
        return ['message' => 'Division updated successfully.'];
    }

//-----------------Party--------------------------------------------------------------------
    /**
     * Add party
     */
    public function addParty(array $data): array
    {
        $party = MemberParty::create([
            'party_en' => $data['partyEn'],
            'party_si' => $data['partySi'],
            'party_ta' => $data['partyTa'],
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return [
            'party' => $party
        ];
    }

    /**
     * Update party
     */
    public function updateParty(int $id, array $data): array
    {
        $party = MemberParty::find($id);
        $party->update([
            'party_en' => $data['partyEn'],
            'party_si' => $data['partySi'],
            'party_ta' => $data['partyTa'],
            'updated_at' => now(),
        ]);
        return ['message' => 'Party updated successfully.'];
    }

    /**
     * Delete party
     */
    public function deleteParty(int $id): bool
    {
        $party = MemberParty::find($id);

        if ($party) {
            $party->delete();
            return true;
        }
        return false;
    }

    //-----------------Position--------------------------------------------------------------------
    /**
     * Add position
     */
    public function addPosition(array $data): array
    {
        $position = MemberPosition::create([
            'position_en' => $data['positionEn'],
            'position_si' => $data['positionSi'],
            'position_ta' => $data['positionTa'],
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return [
            'position' => $position
        ];
    }

    /**
     * Update position
     */
    public function updatePosition(int $id, array $data): array
    {
        $position = MemberPosition::find($id);
        $position->update([
            'position_en' => $data['positionEn'],
            'position_si' => $data['positionSi'],
            'position_ta' => $data['positionTa'],
            'updated_at' => now(),
        ]);
        return ['message' => 'Position updated successfully.'];
    }

    /**
     * Delete position
     */
    public function deletePosition(int $id): bool
    {
        $position = MemberPosition::find($id);

        if ($position) {
            $position->delete();
            return true;
        }
        return false;
    }

    //-----------------Member--------------------------------------------------------------------

    /**
     * Get all members with relationships
     */
    public function getMembers(): Collection
    {
        return $this->model->with([
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
            ->select('members.id', 'title','name_en', 'name_si','name_ta', 'image', 'tel', 'divisions_id', 'member_parties_id', 'user_id')
            ->get();
    }


    /**
     * Create a new member
     */
    public function createMember(Request $request): array
    {
        $user = User::create([
            'email' => $request->email,
        ]);
        $user->assignRole('member');

        // Handle image upload
        $imgPath = null;
        if($request->hasFile('img') && $request->file('img')->isValid()) {
            $image = $request->file('img');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $path = $image->storeAs('images/member', $imageName, 'public');
//            $imagePath = str_replace('storage/', '', $path);
            $imagePath = Storage::url($path);
        }

        // Create member
        $member = $this->create([
            'user_id' => $user->id,
            'name_en' => $request->nameEn,
            'name_si' => $request->nameSi,
            'name_ta' => $request->nameTa,
            'tel' => $request->tel,
            'member_divisions_id' => $request->division,
            'member_parties_id' => $request->party,
            'image' => $imgPath,
        ]);

        // Handle positions
        if ($request->has('position')) {
            $positionIds = is_array($request->input('position'))
                ? $request->input('position')
                : [$request->input('position')];
            $member->memberPositions()->sync($positionIds);
        }

        return [
            'user' => $user,
            'member' => $member,
        ];
    }

    public  function  updateMember($id, $request)
    {
       $existMember = Member::findOrFail($id);

       // Delete existing member image if a new image uploaded
       if ($request->hasFile('img')) {
            // Storage::delete('public/' . $existMember->image);
            $imagePath = $existMember->image;
            $imagePath = str_replace('/storage/', '', $imagePath);
            Storage::disk('public')->delete($imagePath);

            $image = $request->file('img');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $path = $image->storeAs('images/member', $imageName, 'public');
            $imagePathNew = Storage::url($path);
        }else{
            $imagePathNew = $existMember->image;
        }

        $existMember->update([
            'title' => $request['title'],
            'name_en' => $request['nameEn'],
            'name_si' => $request['nameSi'],
            'name_ta' => $request['nameTa'],
            'tel' => $request['tel'],
            'divisions_id' => $request['division'],
            'member_parties_id' => $request['party'],
            'image' => $imagePathNew
        ]);

        if ($request->has('position')) {
            $positionIds = $request->input('position');
            $existMember->memberPositions()->sync($positionIds);
        }

        $user = User::findOrFail($existMember->user_id);
        $user->update([
            // 'email' => $request['email'],
            'status' => $request['status']
        ]);

        return response(['message' => 'Member updated successfully.'], 200);

    }

    /**
     * Delete member and associated user
     */
    public function deleteMember(int $id): bool
    {
        $member = $this->find($id);

        if (!$member) {
            return false;
        }

        $userId = $member->user_id;

        try {
            DB::beginTransaction();

            // Delete member
            $member->delete();

            // Delete associated user
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
     * Update member
     */
    // public function updateMember(int $id, array $data): bool
    // {
    //     return $this->update($id, $data);
    // }

    /**
     * Get members by division
     */
    public function getMembersByDivision(int $divisionId): Collection
    {
        return $this->model->where('member_divisions_id', $divisionId)->get();
    }

    /**
     * Get members by party
     */
    public function getMembersByParty(int $partyId): Collection
    {
        return $this->model->where('member_parties_id', $partyId)->get();
    }

    /**
     * Get members by position
     */
    public function getMembersByPosition(int $positionId): Collection
    {
        return $this->model->whereHas('memberPositions', function ($query) use ($positionId) {
            $query->where('member_positions.id', $positionId);
        })->get();
    }


    public function getCount()
    {
        return Member::count();
    }

}


