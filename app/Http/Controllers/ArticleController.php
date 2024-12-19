<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    public function getAllArticles(Request $request)
    {
        $articles = Article::all();
        return response()->json([
            'status' => 'success',
            'data' => $articles,
        ], 200);
    }
}
