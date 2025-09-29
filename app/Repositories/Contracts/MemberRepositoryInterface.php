<?php

namespace App\Repositories\Contracts;

use App\Models\Member;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface MemberRepositoryInterface extends RepositoryInterface
{
    /**
     * Get all members with relationships
     */
    public function getMembers(): Collection;

    /**
     * Create a new member
     */
    public function createMember(Request $request): array;

    /**
     * Update member
     */
    public function updateMember(int $id, array $data): bool;

    /**
     * Delete member and associated user
     */
    public function deleteMember(int $id): bool;

    /**
     * Get members by division
     */
    public function getMembersByDivision(int $divisionId): Collection;

    /**
     * Get members by party
     */
    public function getMembersByParty(int $partyId): Collection;

    /**
     * Get members by position
     */
    public function getMembersByPosition(int $positionId): Collection;

    /**
     * Add division
     */
    public function addDivision(array $data): array;

    /**
     * Update division
     */
    public function updateDivision(int $id, array $data): array;

    /**
     * Delete division
     */
    public function deleteDivision(int $id): bool;

    /**
     * Add party
     */
    public function addParty(array $data): array;

    /**
     * Update party
     */
    public function updateParty(int $id, array $data): array;

    /**
     * Delete party
     */
    public function deleteParty(int $id): bool;

    /**
     * Add position
     */
    public function addPosition(array $data): array;

    /**
     * Update position
     */
    public function updatePosition(int $id, array $data): array;

    /**
     * Delete position
     */
    public function deletePosition(int $id): bool;
}