<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Articles",
 *     description="API Endpoints for managing news articles"
 * )
 */
class ArticleController extends Controller
{
    /**
     * Fetch all articles with search, filter, and pagination.
     *
     * @OA\Get(
     *     path="/articles",
     *     summary="Get a list of articles",
     *     tags={"Articles"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search keyword in title or content",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filter articles by published date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter articles by category ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="source_id",
     *         in="query",
     *         description="Filter articles by source ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of articles",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Article")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function index(Request $request)
    {
        $query = Article::query();

        // 🔍 Search by keyword
        if ($request->has('q')) {
            $query->where('title', 'ILIKE', "%{$request->q}%")
                  ->orWhere('content', 'ILIKE', "%{$request->q}%");
        }

        // 📆 Filter by date
        if ($request->has('date')) {
            $query->whereDate('published_at', $request->date);
        }

        // 🎭 Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 📰 Filter by source
        if ($request->has('source_id')) {
            $query->where('source_id', $request->source_id);
        }

        // 🛠 Paginate results (default 10 per page)
        $articles = $query->with(['source', 'category'])->paginate(10);

        return response()->json($articles);
    }

    /**
     * Fetch a single article by ID.
     *
     * @OA\Get(
     *     path="/articles/{id}",
     *     summary="Get a single article",
     *     tags={"Articles"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the article",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article details",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(response=404, description="Article not found"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function show($id)
    {
        $article = Article::with(['source', 'category'])->findOrFail($id);
        return response()->json($article);
    }
}
