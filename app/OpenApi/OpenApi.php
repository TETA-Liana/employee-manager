<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'LTA Employee Manager API',
    description: 'Advanced employee management system (LTA) featuring JWT authentication, attendance tracking, and reporting.',

)]
#[OA\Server(
    url: 'http://localhost',
    description: 'Local development server',
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'API Token',
)]
#[OA\Schema(
    schema: 'Employee',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'names', type: 'string', example: 'John Doe'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
        new OA\Property(property: 'employee_identifier', type: 'string', example: 'EMP-001'),
        new OA\Property(property: 'phone_number', type: 'string', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'Attendance',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'employee_id', type: 'integer', example: 1),
        new OA\Property(property: 'check_in_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'check_out_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class OpenApi
{
}

