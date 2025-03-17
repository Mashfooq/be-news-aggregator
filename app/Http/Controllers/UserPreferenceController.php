<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPreference;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="User Preferences",
 *     description="Manage user preferences for news sources and categories"
 * )
 */
class UserPreferenceController extends Controller
{
    /**
     * Store user preferences.
     *
     * @OA\Post(
     *     path="/preferences",
     *     summary="Save user preferences",
     *     tags={"User Preferences"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={},
     *             @OA\Property(property="source_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     *             @OA\Property(property="category_ids", type="array", @OA\Items(type="integer"), example={5, 6})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preferences saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Preferences saved successfully!")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'source_ids'   => 'array',
            'category_ids' => 'array'
        ]);

        $user = Auth::user();

        // Remove existing preferences
        UserPreference::where('user_id', $user->id)->delete();

        // Insert new preferences
        foreach ($request->source_ids ?? [] as $sourceId) {
            UserPreference::create([
                'user_id'   => $user->id,
                'source_id' => $sourceId
            ]);
        }

        foreach ($request->category_ids ?? [] as $categoryId) {
            UserPreference::create([
                'user_id'     => $user->id,
                'category_id' => $categoryId
            ]);
        }

        return response()->json(['message' => 'Preferences saved successfully!']);
    }

    /**
     * Get user preferences.
     *
     * @OA\Get(
     *     path="/preferences",
     *     summary="Get user preferences",
     *     tags={"User Preferences"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="User preferences retrieved",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="sources", type="object", example={"1": "CNN", "2": "BBC"}),
     *                 @OA\Property(property="categories", type="object", example={"5": "Technology", "6": "Sports"})
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index()
    {
        $user = Auth::user();
        $preferences = UserPreference::selectRaw('
            user_id, users.name,
            jsonb_object_agg(sources.id, sources.name) FILTER (WHERE sources.id IS NOT NULL) AS sources,
            jsonb_object_agg(categories.id, categories.name) FILTER (WHERE categories.id IS NOT NULL) AS categories
            ')
            ->leftJoin('sources', 'sources.id', '=', 'user_preferences.source_id')
            ->leftJoin('categories', 'categories.id', '=', 'user_preferences.category_id')
            ->leftJoin('users','users.id','=','user_preferences.user_id')
            ->where('user_id', $user->id)
            ->groupBy('user_id', 'users.name')
            ->get();

        // Decode JSON strings to objects
        $preferences = $preferences->map(function ($item) {
            if (isset($item->sources) && is_string($item->sources)) {
                $item->sources = json_decode($item->sources, true);
            }
            if (isset($item->categories) && is_string($item->categories)) {
                $item->categories = json_decode($item->categories, true);
            }
            return $item;
        });

        return response()->json($preferences);
    }

    /**
     * Get personalized news feed based on user preferences.
     *
     * @OA\Get(
     *     path="/news-feed",
     *     summary="Fetch personalized news feed",
     *     tags={"User Preferences"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="Personalized news feed retrieved",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Breaking News"),
     *                 @OA\Property(property="content", type="string", example="This is the content of the news article."),
     *                 @OA\Property(property="published_at", type="string", format="date-time", example="2025-03-17T10:00:00Z"),
     *                 @OA\Property(property="source", type="object", ref="#/components/schemas/Source"),
     *                 @OA\Property(property="category", type="object", ref="#/components/schemas/Category")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function newsFeed()
    {
        $user = Auth::user();

        $categoryIds = UserPreference::where('user_id', $user->id)
            ->whereNotNull('category_id')
            ->pluck('category_id');

        $sourceIds = UserPreference::where('user_id', $user->id)
            ->whereNotNull('source_id')
            ->pluck('source_id');

        $query = Article::query();

        if ($categoryIds->isNotEmpty()) {
            $query->whereIn('category_id', $categoryIds);
        }

        if ($sourceIds->isNotEmpty()) {
            $query->whereIn('source_id', $sourceIds);
        }

        $articles = $query->with(['source', 'category'])->paginate(10);

        return response()->json($articles);
    }
}
