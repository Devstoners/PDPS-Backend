# PDPS Backend API

This is the backend API for the PDPS (Pradeshiya Sabha Development Project System) application built with Laravel.

## Features

- **Authentication System**: User registration, login, and role-based access control
- **Member Management**: CRUD operations for managing members
- **Complaint System**: Handle and track citizen complaints
- **News Management**: Multi-language news articles (English, Sinhala, Tamil)
- **Hall Management**: Hall reservations and facility management
- **Water Bill Management**: Water scheme and billing system
- **Tax Management**: Tax payee, property, and payment management
- **SMS Notifications**: Twilio integration for SMS services
- **Payment Integration**: PayHere payment gateway integration
- **Project Management**: Development project tracking

## API Documentation

### Swagger UI

The API documentation is available through Swagger UI, providing an interactive interface to explore and test all endpoints.

**Access Swagger UI:**
- **URL**: `http://localhost:8000/api/documentation`
- **Description**: Interactive API documentation with request/response examples

### API Endpoints Overview

#### Authentication
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `POST /api/activate` - Account activation

#### Members
- `GET /api/member` - Get all members
- `POST /api/member` - Create new member
- `PUT /api/member/{id}` - Update member
- `DELETE /api/member/{id}` - Delete member

#### Complaints
- `GET /api/complains` - Get all complaints
- `POST /api/complains` - Submit new complaint
- `PUT /api/complains/{id}` - Update complaint
- `DELETE /api/complains/{id}` - Delete complaint

#### News
- `GET /api/news` - Get all news articles
- `POST /api/news` - Create news article
- `PUT /api/news/{id}` - Update news article
- `DELETE /api/news/{id}` - Delete news article

#### Hall Management
- `GET /api/hall` - Get all halls
- `POST /api/hall` - Create new hall
- `GET /api/halls/availability` - Check hall availability
- `POST /api/reservations` - Create hall reservation

#### Water Bill Management
- `GET /api/water-schemes` - Get water schemes
- `POST /api/water-schemes` - Create water scheme
- `GET /api/water-customers` - Get water customers
- `POST /api/water-customers` - Add water customer

#### Tax Management
- `GET /api/tax-payees` - Get tax payees
- `POST /api/tax-payees` - Create tax payee
- `GET /api/tax-properties` - Get tax properties
- `POST /api/tax-payments` - Process tax payment

#### SMS Notifications
- `POST /api/sms/test` - Send test SMS
- `POST /api/sms/payment-confirmation` - Send payment confirmation SMS
- `POST /api/sms/service-reminder` - Send service reminder SMS

#### Payments
- `POST /api/payments/online` - Process online payment
- `GET /api/payments/{paymentType}/{paymentId}/receipt` - Get payment receipt

## Getting Started

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL/PostgreSQL
- Laravel 10.x

### Installation

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy environment file: `cp .env.example .env`
4. Generate application key: `php artisan key:generate`
5. Configure database in `.env` file
6. Run migrations: `php artisan migrate`
7. Seed database: `php artisan db:seed`
8. Start development server: `php artisan serve`

### Accessing Swagger UI

1. Start the Laravel development server:
   ```bash
   php artisan serve
   ```

2. Open your browser and navigate to:
   ```
   http://localhost:8000/api/documentation
   ```

3. The Swagger UI will display all available API endpoints with:
   - Request/response schemas
   - Example data
   - Interactive testing capabilities
   - Authentication options

### Authentication

Most API endpoints require authentication using Laravel Sanctum. To authenticate:

1. Register a new user via `POST /api/register`
2. Login via `POST /api/login` to get your access token
3. Include the token in the Authorization header:
   ```
   Authorization: Bearer {your-token}
   ```

### Role-Based Access

The API uses role-based access control with the following roles:
- `admin` - Full system access
- `officer` - Officer-level access
- `member` - Member-level access
- `meterReader` - Water meter reading access
- `customerTax` - Tax customer access
- `officerTax` - Tax officer access
- `officerWaterBill` - Water bill officer access
- `officerHallReserve` - Hall reservation officer access

## Development

### Regenerating Swagger Documentation

After adding new API endpoints or modifying existing ones, regenerate the Swagger documentation:

```bash
php artisan l5-swagger:generate
```

### Adding New API Documentation

To document new API endpoints, add Swagger annotations to your controllers:

```php
/**
 * @OA\Get(
 *     path="/your-endpoint",
 *     tags={"Your Tag"},
 *     summary="Your endpoint summary",
 *     description="Your endpoint description",
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Success response",
 *         @OA\JsonContent(...)
 *     )
 * )
 */
public function yourMethod()
{
    // Your method implementation
}
```

## License

This project is licensed under the MIT License.
