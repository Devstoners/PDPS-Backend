# Repository Pattern Implementation Guide

This guide explains how the Repository Pattern has been implemented in this Laravel application to provide a clean separation between data access logic and business logic.

## Overview

The Repository Pattern is a design pattern that abstracts data access logic and provides a more object-oriented view of the persistence layer. It acts as an in-memory collection of domain objects.

## Architecture

### 1. Base Repository Interface (`RepositoryInterface`)

Located at: `app/Repositories/Contracts/RepositoryInterface.php`

This interface defines the common CRUD operations that all repositories should implement:

```php
interface RepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Model;
    public function findOrFail(int $id): Model;
    public function create(array $data): Model;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function where(string $column, $value): Collection;
    public function whereFirst(string $column, $value): ?Model;
    public function count(): int;
    public function exists(int $id): bool;
}
```

### 2. Base Repository Implementation (`BaseRepository`)

Located at: `app/Repositories/BaseRepository.php`

This abstract class provides the default implementation for all common CRUD operations:

```php
abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // Implementation of all interface methods with error handling
}
```

### 3. Specific Repository Interfaces

Each model has its own repository interface that extends the base interface:

- `UserRepositoryInterface` - For user-related operations
- `MemberRepositoryInterface` - For member-related operations  
- `ComplainRepositoryInterface` - For complaint-related operations

### 4. Repository Implementations

Each repository extends the `BaseRepository` and implements its specific interface:

- `UserRepository` - Implements `UserRepositoryInterface`
- `MemberRepository` - Implements `MemberRepositoryInterface`
- `ComplainRepository` - Implements `ComplainRepositoryInterface`

## Usage Examples

### 1. In Controllers

```php
class MemberController extends Controller
{
    private $repository;

    public function __construct(MemberRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $members = $this->repository->getMembers();
        return response(['AllMembers' => $members]);
    }

    public function store(Request $request)
    {
        $response = $this->repository->createMember($request);
        return response($response, 201);
    }
}
```

### 2. Basic CRUD Operations

```php
// Create
$user = $userRepository->create([
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// Find
$user = $userRepository->find(1);
$user = $userRepository->findOrFail(1); // Throws exception if not found

// Update
$userRepository->update(1, ['name' => 'Updated Name']);

// Delete
$userRepository->delete(1);

// Get all
$users = $userRepository->all();

// Count
$count = $userRepository->count();

// Check existence
$exists = $userRepository->exists(1);
```

### 3. Advanced Queries

```php
// Where clauses
$users = $userRepository->where('status', 'active');
$user = $userRepository->whereFirst('email', 'john@example.com');

// Pagination
$users = $userRepository->paginate(10);
```

### 4. Custom Repository Methods

```php
// User-specific methods
$user = $userRepository->findByEmail('john@example.com');
$users = $userRepository->getUsersByRole('admin');
$userRepository->updateStatus(1, 1);

// Member-specific methods
$members = $memberRepository->getMembersByDivision(1);
$members = $memberRepository->getMembersByParty(2);
$memberRepository->addDivision($data);
```

## Service Provider Configuration

The `RepositoryServiceProvider` binds repository interfaces to their implementations:

```php
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, function ($app) {
            return new UserRepository($app->make(User::class));
        });

        $this->app->bind(MemberRepositoryInterface::class, function ($app) {
            return new MemberRepository($app->make(Member::class));
        });

        $this->app->bind(ComplainRepositoryInterface::class, function ($app) {
            return new ComplainRepository($app->make(Complain::class));
        });
    }
}
```

## Benefits

1. **Separation of Concerns**: Data access logic is separated from business logic
2. **Testability**: Easy to mock repositories for unit testing
3. **Flexibility**: Can easily switch between different data sources
4. **Consistency**: All repositories follow the same pattern
5. **Error Handling**: Centralized error handling in base repository
6. **Code Reusability**: Common operations are implemented once in base repository

## Testing

Repository pattern makes testing easier by allowing you to mock the repository interfaces:

```php
public function test_user_creation()
{
    $mockRepository = Mockery::mock(UserRepositoryInterface::class);
    $mockRepository->shouldReceive('create')
        ->once()
        ->andReturn(new User());

    $this->app->instance(UserRepositoryInterface::class, $mockRepository);
    
    // Test your controller or service
}
```

## Best Practices

1. **Always use interfaces**: Inject repository interfaces, not concrete classes
2. **Keep repositories focused**: Each repository should handle one model/entity
3. **Use meaningful method names**: Method names should clearly indicate their purpose
4. **Handle errors gracefully**: Use try-catch blocks and meaningful error messages
5. **Document your methods**: Use PHPDoc comments for better code documentation
6. **Keep business logic out**: Repositories should only handle data access, not business rules

## File Structure

```
app/
├── Repositories/
│   ├── Contracts/
│   │   ├── RepositoryInterface.php
│   │   ├── UserRepositoryInterface.php
│   │   ├── MemberRepositoryInterface.php
│   │   └── ComplainRepositoryInterface.php
│   ├── BaseRepository.php
│   ├── UserRepository.php
│   ├── MemberRepository.php
│   └── ComplainRepository.php
└── Providers/
    └── RepositoryServiceProvider.php
```

This implementation provides a solid foundation for the Repository Pattern in your Laravel application, making it more maintainable, testable, and scalable.