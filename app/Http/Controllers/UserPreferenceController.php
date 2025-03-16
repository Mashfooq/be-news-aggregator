<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPreference;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    // Store user preferences
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

    // Retrieve user preferences
    public function index()
    {
        $user = Auth::user();
        $preferences = UserPreference::where('user_id', $user->id)->get();

        return response()->json($preferences);
    }

    // Fetch personalized news feed based on preferences
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
