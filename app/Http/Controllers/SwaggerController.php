<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="PDPS Backend API",
 *     version="1.0.0",
 *     description="API documentation for PDPS (Pradeshiya Sabha Development Project System) Backend",
 *     @OA\Contact(
 *         email="admin@pdps.lk"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Development Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your API token in the format: Bearer {token}"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication related endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Members",
 *     description="Member management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Complaints",
 *     description="Complaint management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="News",
 *     description="News management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Hall Management",
 *     description="Hall reservation and management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Water Bill Management",
 *     description="Water bill and customer management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Tax Management",
 *     description="Tax system management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="SMS Notifications",
 *     description="SMS notification endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Payments",
 *     description="Payment processing endpoints"
 * )
 */
class SwaggerController extends Controller
{
    // This controller is used only for Swagger documentation
}
