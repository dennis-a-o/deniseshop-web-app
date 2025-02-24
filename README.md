
# Deniseshop web app 

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.0+-%23777BB4.svg)](https://php.net)

A modern e-commerce solution featuring an admin panel and RESTful API, built with Laravel 10. Includes JWT authentication, PayPal integration, and PDF generation capabilities.

## Features

- **Admin Panel**
  - Product Management
  - Order Management
  - User Management
  - Category Management
  - Sales Analytics
  - PDF Report Generation

- **RESTful API**
  - JWT Authentication
  - Product Catalog
  - Shopping Cart System
  - Order Processing
  - Payment Integration (PayPal)
  - User Registration/Login

## Prerequisites

- PHP 8.0 or higher
- Composer
- MySQL/MariaDB
- Web Server (Apache/Nginx)
- Required PHP Extensions:
  - OpenSSL
  - PDO
  - Mbstring
  - Tokenizer
  - JSON
  - cURL
  - ZIP

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/dennis-a-o/deniseshop-web-app.git
   cd deniseshop
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure Database**
   Update your `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_db_name
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   ```

5. **JWT Configuration**
   ```bash
   php artisan jwt:secret
   ```

6. **Database Migration & Seeding**
   ```bash
   php artisan migrate --seed
   ```
7. **Using Web installer**
  - Create new database e.g deniseshop_db
  - Setup .env file as following(important)
   ```env
   APP_NAME=YourAppName
   APP_ENV=local
   APP_KEY=base64:K836NJjPxQK138O07SRvMfyQRaJFF6be0tME4v2zErc=
   APP_DEBUG=true
   APP_URL=http://yourdomain.com
   ```
   - open the link in browser 'https://yourdomain.com/install' to complete the installation wizard.
   - open login link 'https://yourdomain.com/login' email=admin@localhost.com password = 12345678 and you will directed to admin panel.

## Packages Included(.composer.json)

- **Authentication**: `tymon/jwt-auth` (JWT Authentication)
- **Payments**: `paypal/paypal-http-sdk` (PayPal Integration)
- **PDF Generation**: `barryvdh/laravel-dompdf` (PDF Reports)

## Configuration

### JWT Setup
Add to `.env`:
```env
JWT_SECRET=your_generated_secret
JWT_TTL=60
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin panel controllers
│   │   └── API/           # API controllers
│   ├── Middleware/       # Admin , Intaller middlewares
├── Models/                 # Eloquent models
├── Helpers/                 # Custom utils
├── Services/                 # Custom servics
database/
├── migrations/             # Database migrations
├── seeders/                # Database seeders
routes/
├── web.php                 # Admin panel routes
└── api.php                 # API routes
config/
├── jwt.php                 # JWT configuration
└── paypal.php              # PayPal configuration
```

## API Documentation

### Authentication
```http
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

Example Response:
```json
{
   "success": true,
   "message":"Logged...",
   "api_token": {
      "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9......"
    },
    "user": {
        "id": 16,
        "name": "John Dark",
        "email": "user@localhost.com",
        "phone": "1234567890",
     }
}
```

### Example API Request
```bash
curl -X GET http://localhost:8000/api/products \
     -H "Authorization: Bearer your_jwt_token" \
     -H "Content-Type: application/json"
```

## Admin Panel Features

1. **Dashboard**
   - Sales Overview
   - Recent Orders
   - Product Statistics

2. **Product Management**
   - Create/Edit Products
   - Manage Categories

3. **Order Management**
   - Process Orders
   - Update Order Status
   - Generate PDF Invoices

4. **User Management**
   - Manage Admin Users
   - Customer Overview
   - Role Management

## Deployment

1. **Server Requirements**
   - Ensure all PHP extensions are installed
   - Set proper file permissions:
     ```bash
     chmod -R 755 storage
     chmod -R 755 bootstrap/cache
     ```
     
2. **Optimization**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
## Security

- Use HTTPS in production
- Regularly update dependencies
- Store sensitive data in `.env`
- Use strong JWT secrets
- Validate all API requests

## Client

The Deniseshop web App serves the [Deniseshop Android App](https://github.com/dennis-a-o/deniseshop.git) for RESTful api.</br>

## License

MIT License. See [LICENSE](LICENSE) for more information.

## Acknowledgments

- Laravel Framework
- Tymon JWT-Auth
- PayPal SDK
- DomPDF
