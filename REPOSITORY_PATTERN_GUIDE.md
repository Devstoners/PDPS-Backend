# Repository Design Pattern Implementation Guide

## Overview

This Laravel project now implements the Repository Design Pattern following best practices. The pattern provides a clean separation between your business logic and data access layer, making your code more maintainable, testable, and flexible.

## Architecture Overview

### 1. Repository Interface (`app/Contracts/RepositoryInterface.php`)
Defines the contract that all repositories must implement, ensuring consistency across the application.

### 2. Base Repository (`app/Repositories/BaseRepository.php`)
Abstract class that implements common CRUD operations and utilities that all repositories inherit.

### 3. Specific Repositories
Each model has its own repository that extends `BaseRepository` and provides model-specific methods.

### 4. Service Layer
Business logic is handled in service classes that orchestrate repository operations.

### 5. Controllers
Slim controllers that delegate work to services and handle HTTP concerns.

## Directory Structure

```
app/
├── Contracts/
│   └── RepositoryInterface.php
├── Repositories/
│   ├── BaseRepository.php
│   ├── UserRepository.php
│   ├── ComplainRepository.php
│   ├── TaxRepository.php
│   ├── PaymentRepository.php
│   ├── ProjectRepository.php
│   ├── NewsRepository.php
│   └── MemberRepository.php
├── Services/
│   ├── UserService.php
│   ├── ComplainService.php
│   └── TaxService.php
├── Providers/
│   └── RepositoryServiceProvider.php
└── Http/Controllers/
    └── AuthController.php (refactored)
```

## Key Features

### Repository Interface Methods

All repositories implement these standard methods:

- `all()` - Get all records
- `paginate()` - Get paginated records
- `find()` - Find by ID
- `findOrFail()` - Find by ID or throw exception
- `findByCriteria()` - Find by criteria array
- `create()` - Create new record
- `update()` - Update existing record
- `delete()` - Delete record
- `updateOrCreate()` - Update or create record
- `count()` - Count records
- `exists()` - Check if record exists
- `where()` - Query with where clause
- `whereIn()` - Query with whereIn clause
- `orderBy()` - Order results
- `latest()` - Get latest records
- `oldest()` - Get oldest records

### Service Layer Benefits

- **Business Logic Centralization**: All business rules are in services
- **Transaction Management**: Services handle database transactions
- **Email/Notification Logic**: Services manage communications
- **Data Validation**: Complex validation logic resides in services
- **Multiple Repository Coordination**: Services can use multiple repositories

## Usage Examples

### 1. Basic Repository Usage

```php
// In a controller or service
$userRepository = app(UserRepository::class);

// Get all users
$users = $userRepository->all();

// Find user by ID
$user = $userRepository->find(1);

// Create new user
$user = $userRepository->create([
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// Update user
$user = $userRepository->update(1, ['name' => 'Jane Doe']);

// Find by criteria
$activeUsers = $userRepository->findByCriteria(['status' => 1]);
```

### 2. Custom Repository Methods

```php
// UserRepository specific methods
$userRepository = app(UserRepository::class);

// Find by email
$user = $userRepository->findByEmail('john@example.com');

// Get active users
$activeUsers = $userRepository->getActiveUsers();

// Create user with role
$user = $userRepository->createWithRole($data, 'admin');
```

### 3. Service Layer Usage

```php
// In a controller
class AuthController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        $response = $this->userService->registerUser($request->validated());
        return response()->json($response, 201);
    }
}
```

### 4. Advanced Queries

```php
// Using query callbacks for complex queries
$users = $userRepository->where_callback(function($query) {
    return $query->where('created_at', '>', now()->subDays(30))
                 ->where('status', 1)
                 ->whereHas('roles', function($q) {
                     $q->where('name', 'admin');
                 });
})->getQuery()->get();
```

## Service Examples

### UserService
- User registration with role assignment
- Authentication with proper error handling
- Account activation
- Password management
- User statistics

### ComplainService
- Complain creation with notifications
- Search and filtering
- Statistics and reporting
- Email notifications

### TaxService
- Tax payment processing
- Transaction management
- Payment history
- Reporting and analytics

## Benefits of This Implementation

### 1. **Separation of Concerns**
- Controllers handle HTTP requests/responses
- Services handle business logic
- Repositories handle data access
- Models represent data structure

### 2. **Testability**
- Easy to mock repositories in tests
- Services can be tested independently
- Clear interfaces make testing straightforward

### 3. **Maintainability**
- Changes to data access don't affect business logic
- Business logic changes don't affect data layer
- Clear code organization

### 4. **Flexibility**
- Easy to switch data sources
- Can add caching layers
- Database agnostic business logic

### 5. **Reusability**
- Repository methods can be reused across services
- Service methods can be reused across controllers
- Common patterns are standardized

## Best Practices

### 1. **Repository Guidelines**
- Keep repositories focused on data access
- Don't put business logic in repositories
- Use specific methods for complex queries
- Always extend BaseRepository

### 2. **Service Guidelines**
- Put all business logic in services
- Handle transactions in services
- Manage external communications (email, SMS)
- Validate complex business rules

### 3. **Controller Guidelines**
- Keep controllers thin
- Delegate to services
- Handle only HTTP concerns
- Return consistent response formats

### 4. **Error Handling**
- Use appropriate exceptions
- Handle errors at the service level
- Return meaningful error messages
- Log errors appropriately

## Adding New Repositories

### 1. Create Repository Class

```php
<?php

namespace App\Repositories;

use App\Models\YourModel;
use App\Repositories\BaseRepository;

class YourModelRepository extends BaseRepository
{
    public function model(): string
    {
        return YourModel::class;
    }

    // Add model-specific methods here
    public function findByCustomField($value)
    {
        return $this->findFirstByCriteria(['custom_field' => $value]);
    }
}
```

### 2. Register in Service Provider

```php
// In RepositoryServiceProvider.php
$this->app->singleton(YourModelRepository::class, function ($app) {
    return new YourModelRepository();
});
```

### 3. Create Service (Optional)

```php
<?php

namespace App\Services;

use App\Repositories\YourModelRepository;

class YourModelService
{
    protected YourModelRepository $repository;

    public function __construct(YourModelRepository $repository)
    {
        $this->repository = $repository;
    }

    // Add business logic methods here
}
```

## Migration from Old Pattern

The old `Repository.php` file has been removed and its functionality distributed properly:

- **User management** → `UserService` and `UserRepository`
- **Tax operations** → `TaxService` and `TaxRepository`
- **Authentication** → `UserService`
- **Data access** → Specific repositories

## Testing

### Repository Testing

```php
// Test repository methods
public function test_can_find_user_by_email()
{
    $user = User::factory()->create(['email' => 'test@example.com']);
    $repository = new UserRepository();
    
    $foundUser = $repository->findByEmail('test@example.com');
    
    $this->assertEquals($user->id, $foundUser->id);
}
```

### Service Testing

```php
// Test service methods with mocked repositories
public function test_user_registration()
{
    $mockRepository = $this->createMock(UserRepository::class);
    $service = new UserService($mockRepository);
    
    // Test business logic
}
```

## Performance Considerations

### 1. **Eager Loading**
Always specify relationships when needed:
```php
$users = $userRepository->all(['*'], ['roles', 'permissions']);
```

### 2. **Query Optimization**
Use specific columns when possible:
```php
$users = $userRepository->all(['id', 'name', 'email']);
```

### 3. **Caching**
Implement caching at the service level:
```php
public function getActiveUsers()
{
    return Cache::remember('active_users', 3600, function() {
        return $this->userRepository->getActiveUsers();
    });
}
```

## Conclusion

This repository pattern implementation provides a robust, maintainable, and testable foundation for your Laravel application. It follows SOLID principles and provides clear separation of concerns while maintaining flexibility for future changes.

The pattern is now fully integrated into your application with proper dependency injection, service registration, and clean architecture principles.