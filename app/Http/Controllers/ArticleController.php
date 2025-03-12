<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // Fetch all articles with search, filter, pagination
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

    // Fetch a single article
    public function show($id)
    {
        $article = Article::with(['source', 'category'])->findOrFail($id);
        return response()->json($article);
    }
}
