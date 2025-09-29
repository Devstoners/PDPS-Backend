<?php

namespace App\Repositories;

use App\Models\Member;
use App\Models\Division;
use App\Models\MemberParty;
use App\Models\MemberPosition;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MemberRepository extends BaseRepository
{
    /**
     * Specify Model class name
     * @return string
     */
    public function model(): string
    {
        return Member::class;
    }

    /**
     * Get all members with relationships
     * @param array $columns
     * @return Collection
     */
    public function getAllMembersWithRelations(array $columns = ['*']): Collection
    {
        return $this->getQuery()
            ->with([
                'memberDivision:id,division_en',
                'memberParty:id,party_en',
                'memberPositions:id,position_en',
                'user:id,email,status'
            ])
            ->select('members.id', 'name_en', 'name_si', 'name_ta', 'image', 'tel', 'member_divisions_id', 'member_parties_id', 'user_id')
            ->get();
    }

    /**
     * Create member with user account
     * @param array $data
     * @param array $positionIds
     * @return array
     */
    public function createMemberWithUser(array $data, array $positionIds = []): array
    {
        return DB::transaction(function () use ($data, $positionIds) {
            // Create user account
            $user = User::create([
                'email' => $data['email'],
                'status' => 0, // Inactive by default
            ]);
            $user->assignRole('member');

            // Handle image upload
            $imgPath = null;
            if (isset($data['image']) && $data['image']) {
                $imgPath = $this->handleImageUpload($data['image']);
            }

            // Create member
            $member = $this->create([
                'user_id' => $user->id,
                'name_en' => $data['nameEn'] ?? $data['name_en'],
                'name_si' => $data['nameSi'] ?? $data['name_si'],
                'name_ta' => $data['nameTa'] ?? $data['name_ta'],
                'tel' => $data['tel'],
                'member_divisions_id' => $data['division'] ?? $data['member_divisions_id'],
                'member_parties_id' => $data['party'] ?? $data['member_parties_id'],
                'image' => $imgPath,
            ]);

            // Sync positions
            if (!empty($positionIds)) {
                $member->memberPositions()->sync($positionIds);
            }

            return [
                'user' => $user,
                'member' => $member->load(['memberDivision', 'memberParty', 'memberPositions']),
            ];
        });
    }

    /**
     * Update member information
     * @param int $memberId
     * @param array $data
     * @param array $positionIds
     * @return Member
     */
    public function updateMemberWithPositions(int $memberId, array $data, array $positionIds = []): Member
    {
        return DB::transaction(function () use ($memberId, $data, $positionIds) {
            $member = $this->findOrFail($memberId);

            // Handle image upload if provided
            if (isset($data['image']) && $data['image']) {
                // Delete old image if exists
                if ($member->image) {
                    $this->deleteImage($member->image);
                }
                $data['image'] = $this->handleImageUpload($data['image']);
            }

            // Update member
            $updatedMember = $this->update($memberId, $data);

            // Sync positions if provided
            if (!empty($positionIds)) {
                $updatedMember->memberPositions()->sync($positionIds);
            }

            return $updatedMember->load(['memberDivision', 'memberParty', 'memberPositions']);
        });
    }

    /**
     * Delete member and associated user
     * @param int $memberId
     * @return bool
     */
    public function deleteMemberWithUser(int $memberId): bool
    {
        return DB::transaction(function () use ($memberId) {
            $member = $this->findOrFail($memberId);
            $userId = $member->user_id;

            // Delete member image if exists
            if ($member->image) {
                $this->deleteImage($member->image);
            }

            // Delete member
            $this->delete($memberId);

            // Delete associated user
            $user = User::find($userId);
            if ($user) {
                $user->tokens()->delete();
                $user->roles()->detach();
                $user->permissions()->detach();
                $user->delete();
            }

            return true;
        });
    }

    /**
     * Get members by division
     * @param int $divisionId
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getMembersByDivision(int $divisionId, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->findByCriteria(['member_divisions_id' => $divisionId], $columns, $relations);
    }

    /**
     * Get members by party
     * @param int $partyId
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getMembersByParty(int $partyId, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->findByCriteria(['member_parties_id' => $partyId], $columns, $relations);
    }

    /**
     * Get members by position
     * @param int $positionId
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getMembersByPosition(int $positionId, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->where_callback(function($query) use ($positionId) {
            return $query->whereHas('memberPositions', function($q) use ($positionId) {
                $q->where('member_positions.id', $positionId);
            });
        })->getQuery()->with($relations)->get($columns);
    }

    /**
     * Search members by name
     * @param string $searchTerm
     * @param string $language
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function searchMembersByName(string $searchTerm, string $language = 'en', array $columns = ['*'], array $relations = []): Collection
    {
        $nameColumn = "name_{$language}";
        return $this->where_callback(function($query) use ($nameColumn, $searchTerm) {
            return $query->where($nameColumn, 'LIKE', "%{$searchTerm}%");
        })->getQuery()->with($relations)->get($columns);
    }

    /**
     * Get active members (users with status = 1)
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getActiveMembers(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->where_callback(function($query) {
            return $query->whereHas('user', function($q) {
                $q->where('status', 1);
            });
        })->getQuery()->with($relations)->get($columns);
    }

    /**
     * Handle image upload
     * @param $image
     * @return string
     */
    private function handleImageUpload($image): string
    {
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('images', $imageName, 'public');
        return Storage::url($path);
    }

    /**
     * Delete image file
     * @param string $imagePath
     * @return bool
     */
    private function deleteImage(string $imagePath): bool
    {
        $relativePath = str_replace('/storage/', '', $imagePath);
        return Storage::disk('public')->delete($relativePath);
    }

    /**
     * Get member statistics
     * @return array
     */
    public function getMemberStatistics(): array
    {
        return [
            'total_members' => $this->count(),
            'active_members' => $this->getActiveMembers()->count(),
            'by_division' => MemberDivision::withCount('members')->get(),
            'by_party' => MemberParty::withCount('members')->get(),
            'by_position' => MemberPosition::withCount('members')->get(),
        ];
    }
}

/**
 * Division Repository Methods
 */
class MemberDivisionRepository extends BaseRepository
{
    public function model(): string
    {
        return MemberDivision::class;
    }

    public function createDivision(array $data): MemberDivision
    {
        return $this->create([
            'division_en' => $data['divisionEn'] ?? $data['division_en'],
            'division_si' => $data['divisionSi'] ?? $data['division_si'],
            'division_ta' => $data['divisionTa'] ?? $data['division_ta'],
        ]);
    }

    public function updateDivision(int $divisionId, array $data): MemberDivision
    {
        return $this->update($divisionId, [
            'division_en' => $data['divisionEn'] ?? $data['division_en'],
            'division_si' => $data['divisionSi'] ?? $data['division_si'],
            'division_ta' => $data['divisionTa'] ?? $data['division_ta'],
        ]);
    }
}

/**
 * Party Repository Methods
 */
class MemberPartyRepository extends BaseRepository
{
    public function model(): string
    {
        return MemberParty::class;
    }

    public function createParty(array $data): MemberParty
    {
        return $this->create([
            'party_en' => $data['partyEn'] ?? $data['party_en'],
            'party_si' => $data['partySi'] ?? $data['party_si'],
            'party_ta' => $data['partyTa'] ?? $data['party_ta'],
        ]);
    }

    public function updateParty(int $partyId, array $data): MemberParty
    {
        return $this->update($partyId, [
            'party_en' => $data['partyEn'] ?? $data['party_en'],
            'party_si' => $data['partySi'] ?? $data['party_si'],
            'party_ta' => $data['partyTa'] ?? $data['party_ta'],
        ]);
    }
}

/**
 * Position Repository Methods
 */
class MemberPositionRepository extends BaseRepository
{
    public function model(): string
    {
        return MemberPosition::class;
    }

    public function createPosition(array $data): MemberPosition
    {
        return $this->create([
            'position_en' => $data['positionEn'] ?? $data['position_en'],
            'position_si' => $data['positionSi'] ?? $data['position_si'],
            'position_ta' => $data['positionTa'] ?? $data['position_ta'],
        ]);
    }

    public function updatePosition(int $positionId, array $data): MemberPosition
    {
        return $this->update($positionId, [
            'position_en' => $data['positionEn'] ?? $data['position_en'],
            'position_si' => $data['positionSi'] ?? $data['position_si'],
            'position_ta' => $data['positionTa'] ?? $data['position_ta'],
        ]);
    }
}
