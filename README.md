# Event_Ticket_Service_RESTful_API

## Description

This is a RESTful API for an event ticket service. It allows users to register, login, and logout, as well as view events and purchase tickets for events.

## Dependencies

- Docker (https://www.docker.com/products/docker-desktop/)
- Composer (https://getcomposer.org/)
- PHP 8.2 (https://www.php.net/downloads)
- Laravel 10 (https://laravel.com/docs/10.x/installation)
- MySQL (https://www.mysql.com/downloads/)
- Postman (optional) (https://www.postman.com/downloads/)

## Installation

1. Clone the repository
2. Run `composer install`
3. Run `docker compose up -d`
4. Run `php artisan migrate`
5. Run `php artisan db:seed` (A user to test the API is created with email `admin@admin.com` and password `password`, also a list of events is created)

## API Documentation

To view the API documentation and API endpoints, you can use the Swagger UI at `http://localhost:80/api/v1/docs`.

# Postman Collection

You can import the `Test collection.postman_collection.json` file into Postman to test the API endpoints.

# API Endpoints

- `POST /api/v1/login` - Login to the API
- `POST /api/v1/logout` - Logout from the API
- `POST /api/v1/register` - Register a new user
- `GET /api/v1/events` - Get all events
- `GET /api/v1/events/{id}` - Get a specific event
- `POST /api/v1/events` - Create a new event
- `PUT /api/v1/events/{id}` - Update an existing event
- `DELETE /api/v1/events/{id}` - Delete an existing event
- `POST /api/v1/reservations` - Create a new reservation
- `GET /api/v1/reservations` - Get all reservations
- `GET /api/v1/reservations/{id}` - Get a specific reservation
- `PUT /api/v1/reservations/{id}` - Update an existing reservation
- `DELETE /api/v1/reservations/{id}` - Delete an existing reservation

