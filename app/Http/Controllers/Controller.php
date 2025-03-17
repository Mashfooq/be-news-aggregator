<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="News Aggregator API",
 *         version="1.0",
 *         description="This is the API documentation for the News Aggregator.",
 *         @OA\Contact(
 *             email="support@example.com"
 *         )
 *     ),
 *     @OA\Server(
 *         url=L5_SWAGGER_CONST_HOST,
 *         description="API Server"
 *     ),
 *     @OA\Components(
 *         @OA\SecurityScheme(
 *             securityScheme="bearerAuth",
 *             type="http",
 *             scheme="bearer"
 *         ),
 *         @OA\Schema(
 *             schema="Article",
 *             type="object",
 *             @OA\Property(property="id", type="integer", format="int64"),
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="content", type="string"),
 *             @OA\Property(property="url", type="string", format="url"),
 *             @OA\Property(property="image_url", type="string", format="url", nullable=true),
 *             @OA\Property(property="source_id", type="integer"),
 *             @OA\Property(property="category_id", type="integer"),
 *             @OA\Property(property="published_at", type="string", format="date-time"),
 *             @OA\Property(property="created_at", type="string", format="date-time"),
 *             @OA\Property(property="updated_at", type="string", format="date-time")
 *         ),
 *         @OA\Schema(
 *             schema="User",
 *             type="object",
 *             @OA\Property(property="id", type="integer", format="int64"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *             @OA\Property(property="created_at", type="string", format="date-time"),
 *             @OA\Property(property="updated_at", type="string", format="date-time")
 *         ),
 *         @OA\Schema(
 *             schema="Source",
 *             type="object",
 *             @OA\Property(property="id", type="integer", format="int64"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="url", type="string", format="url"),
 *             @OA\Property(property="created_at", type="string", format="date-time"),
 *             @OA\Property(property="updated_at", type="string", format="date-time")
 *         ),
 *         @OA\Schema(
 *             schema="Category",
 *             type="object",
 *             @OA\Property(property="id", type="integer", format="int64"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="created_at", type="string", format="date-time"),
 *             @OA\Property(property="updated_at", type="string", format="date-time")
 *         )
 *     )
 * )
 */
class Controller
{
    use AuthorizesRequests, ValidatesRequests;
}
