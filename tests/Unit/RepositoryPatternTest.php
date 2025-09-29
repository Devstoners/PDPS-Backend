<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\MemberRepositoryInterface;
use App\Repositories\Contracts\ComplainRepositoryInterface;
use App\Models\User;
use App\Models\Member;
use App\Models\Complain;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RepositoryPatternTest extends TestCase
{
    use RefreshDatabase;

    protected $userRepository;
    protected $memberRepository;
    protected $complainRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->userRepository = app(UserRepositoryInterface::class);
        $this->memberRepository = app(MemberRepositoryInterface::class);
        $this->complainRepository = app(ComplainRepositoryInterface::class);
    }

    /**
     * Test that repository interfaces are properly bound
     */
    public function test_repository_interfaces_are_bound()
    {
        $this->assertInstanceOf(UserRepositoryInterface::class, $this->userRepository);
        $this->assertInstanceOf(MemberRepositoryInterface::class, $this->memberRepository);
        $this->assertInstanceOf(ComplainRepositoryInterface::class, $this->complainRepository);
    }

    /**
     * Test basic CRUD operations on User repository
     */
    public function test_user_repository_crud_operations()
    {
        // Test create
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'status' => 1,
            'type' => 'customer',
            'requesttype' => '3'
        ];

        $user = $this->userRepository->create($userData);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);

        // Test find
        $foundUser = $this->userRepository->find($user->id);
        $this->assertEquals($user->id, $foundUser->id);

        // Test update
        $updateData = ['name' => 'Updated User'];
        $updated = $this->userRepository->update($user->id, $updateData);
        $this->assertTrue($updated);

        // Test delete
        $deleted = $this->userRepository->delete($user->id);
        $this->assertTrue($deleted);
    }

    /**
     * Test repository count functionality
     */
    public function test_repository_count()
    {
        // Create some test users
        User::factory()->count(3)->create();

        $count = $this->userRepository->count();
        $this->assertEquals(3, $count);
    }

    /**
     * Test repository exists functionality
     */
    public function test_repository_exists()
    {
        $user = User::factory()->create();
        
        $this->assertTrue($this->userRepository->exists($user->id));
        $this->assertFalse($this->userRepository->exists(999));
    }

    /**
     * Test repository where functionality
     */
    public function test_repository_where()
    {
        User::factory()->create(['email' => 'test1@example.com']);
        User::factory()->create(['email' => 'test2@example.com']);
        User::factory()->create(['email' => 'other@example.com']);

        $users = $this->userRepository->where('email', 'like', '%test%');
        $this->assertCount(2, $users);
    }
}