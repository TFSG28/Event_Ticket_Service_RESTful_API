# Event_Ticket_Service_RESTful_API

## Description

This is a RESTful API for an event ticket service. It allows users to register, login, and logout, as well as view events and purchase tickets for events.

## Dependencies

- Docker (https://www.docker.com/products/docker-desktop/)
- Composer (https://getcomposer.org/)
- PHP 8.2 (https://www.php.net/downloads)
- Laravel 10 (https://laravel.com/docs/10.x/installation)
- MySQL (https://www.mysql.com/downloads/)
- Postman (https://www.postman.com/downloads/)

## Installation

1. Clone the repository
2. Run `composer install`
3. Run `docker compose up -d`
4. Run `php artisan migrate`
5. Run `php artisan db:seed` (A user to test the API is created with email `admin@admin.com` and password `password`)

## API Documentation

To view the API documentation and API endpoints, you can use the Swagger UI at `http://localhost:80/api/v1/docs`.

# Postman Collection

You can import the `Test collection.postman_collection.json` file into Postman to test the API endpoints.

