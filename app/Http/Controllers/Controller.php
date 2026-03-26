<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "EduFlow API Documentation",
    description: "API documentation for the EduFlow pedagogical management solution."
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "EduFlow API Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Enter JWT Bearer token **_only_**"
)]
abstract class Controller
{
    //
}
