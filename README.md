# Home Connect CRM API

## Overview
The Home Connect CRM API provides a set of RESTful endpoints for managing leads, reservations, and properties in a real estate business. It allows sales agents and admins to track leads, assign agents, manage property reservations, and handle financial/legal approvals.

This API follows best REST practices and uses Laravel Sanctum for authentication. All endpoints requiring authentication must include a Bearer Token in the Authorization header.

## Prerequisites
- PHP 8.2 or later
- Composer
- MySQL
- Laravel 10
- Postman (for API testing)

## Installation
1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd home-connect-crm
   ```
2. Install dependencies:
   ```bash
   composer install
   ```
3. Set up environment variables:
   ```bash
   cp .env.example .env
   ```
   Update `.env` with your database credentials:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=home_connect_crm
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```
4. Generate application key:
   ```bash
   php artisan key:generate
   ```
5. Run database migrations:
   ```bash
   php artisan migrate
   ```
6. Serve the application:
   ```bash
   php artisan serve
   ```

## Authentication
Authentication is handled via Laravel Sanctum. To obtain a Bearer Token, users must first register and log in.

### Register User
```bash
POST /api/register
```
Request Body:
```json
{
  "name": "Lakshitha Mathngadeera",
  "email": "lmathngadeera@gmail.com",
  "password": "password123"
}
```

### Login User
```bash
POST /api/login
```
Request Body:
```json
{
  "email": "lmathngadeera@gmail.com",
  "password": "password123"
}
```
Response:
```json
{
  "token": "your-generated-token"
}
```
Include the token in the Authorization header for all protected routes:
```
Authorization: Bearer your-generated-token
```

## API Endpoints
### Leads
- **Create Lead**: `POST /api/leads`
- **Assign Lead**: `PUT /api/leads/{lead_id}/assign`
- **Progress Lead**: `PUT /api/leads/{lead_id}/progress`
- **Cancel Lead**: `PUT /api/leads/{lead_id}/cancel`
- **Get Lead by ID**: `GET /api/leads/{lead_id}`
- **Get All Leads**: `GET /api/leads`

### Reservations
- **Create Reservation**: `POST /api/reservations`
- **Approve Financials**: `PUT /api/reservations/{reservation_id}/approve-financials`
- **Finalize Legal**: `PUT /api/reservations/{reservation_id}/finalize-legal`
- **Get Reservations**: `GET /api/reservations?financial_status=Approved`

### Properties
- **Create Property**: `POST /api/properties`
- **Update Property**: `PUT /api/properties/{property_id}`
- **Delete Property**: `DELETE /api/properties/{property_id}`
- **Get Properties**: `GET /api/properties?status=Available`

## Testing
### Run PHPUnit Tests
Execute unit tests with:
```bash
php artisan test
```

### Postman Testing
- Import the provided Postman Collection
- Use a Bearer Token for authentication in requests
- Verify API responses match expected formats

## Deployment
1. Set up a production environment with MySQL and PHP 8.2
2. Configure environment variables in `.env`
3. Run database migrations:
   ```bash
   php artisan migrate --force
   ```
4. Start the Laravel application with a process manager like Supervisor or serve it with Nginx/Apache.

## License
This project is licensed under the MIT License.
