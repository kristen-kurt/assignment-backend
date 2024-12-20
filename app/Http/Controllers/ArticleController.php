<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{
    public function getAllArticles(): JsonResponse
    {
        $articles = Article::paginate(12);
        return response()->json([
            'status' => 'success',
            'data' => ArticleResource::collection($articles->items()),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
                'last_page' => $articles->lastPage(),
            ],
        ], 200);
    }

    public function filterArticles(Request $request): JsonResponse
    {
        $category=null;$source=null;$dateFilter=null;
        $validated = $request->validate(['keyword' => ['nullable', 'string', 'max:255']]);
        if(isset($request->category))
        {
            $validated = $request->validate([
                'category' => ['required', 'string', 'max:255']
            ]);
            $category = $validated['category'] ;

        }
        if(isset($request->source))
        {
            $validated = $request->validate([
                'source' => ['required', 'string', 'max:255']
            ]);
            $source = $validated['source'] ;

        }
        if(isset($request->date_filter))
        {
            $validated = $request->validate([
                'date_filter' => ['required', 'string', 'max:255']
            ]);
            $dateFilter = $validated['date_filter'] ;
        }
        
        $keyword = $validated['keyword'] ?? null;
        
        $articles = Article::query()
            ->when($category, function ($query, $category) {
                $query->where('category', $category);
            })
            ->when($keyword, function ($query, $keyword) {
                $query->where('article_title', 'LIKE', "%{$keyword}%");
            })
            ->when($source, function ($query, $source) {
                $query->where('site_name', $source);
            })
            ->when($dateFilter, function ($query, $dateFilter) {
                if($dateFilter === 'Today')
                {
                    $query->whereDate('published_at', Carbon::today());
                }
                if($dateFilter === 'Yesterday')
                {
                    $query->whereDate('published_at', Carbon::yesterday());
                }
            })
            ->whereDate('created_at', Carbon::today())
            ->get();

        return response()->json([
            'success' => true,
            'data' => $articles,
        ]);
    }
    public function getCategories(){
        $categories = Category::all();
        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ], 200);
    }
    public function getSources(){
        $sources = Source::all();
        return response()->json([
            'status' => 'success',
            'data' => $sources,
        ], 200);
    }
}
