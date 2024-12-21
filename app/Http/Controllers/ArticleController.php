<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{

    public function getAllArticles(Request $request): JsonResponse
    {   
       if( isset($request->category_id) || isset($request->source_id)){
         $articles = $this->getArticlesWithNoPreferences($request);
       }
       else {
        $articles =  $this->getArticlesWithPreferences($request);
       }
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
    private function getArticlesWithNoPreferences(Request $request){
        $category_id = (int)$request->category_id;
        $source_id = (int)$request->source_id;
        $keyword = $request->keyword;

        $startAndEndDate = $this->getStartAndEndDate($request->selected_date);

        $query = Article::query();
        if ($category_id) {
            $query->where('category_id', $category_id);
        }
        if ($source_id) {
            $query->where('source_id', $source_id);
        }
        if ($keyword) {
            $query->where('article_title', 'LIKE', "%{$keyword}%");
        }
        if ($request->selected_date) {
            $query->whereBetween('published_at', [$startAndEndDate['startDate'], $startAndEndDate['endDate']]);
        }
        $articles = $query->orderBy('published_at','desc')->paginate(12);
        
        return $articles;
    }
    private function getArticlesWithPreferences(Request $request){
        $user = auth()->user();

        $categoryIds = $user->categories->pluck('id')->toArray();
        $sourceIds = $user->sources->pluck('id')->toArray();
        $authorIds = $user->authors->pluck('id')->toArray();
        $keyword = $request->keyword;

        $startAndEndDate = $this->getStartAndEndDate($request->selected_date);

        $articles = Article::query()
            ->when(!empty($keyword), function ($query) use ($keyword) {
                $query->where('article_title', 'LIKE', "%{$keyword}%");
            })
            ->when(!empty($request->selected_date), function ($query) use ($startAndEndDate) {
                $query->whereBetween('published_at', [$startAndEndDate['startDate'], $startAndEndDate['endDate']]);
            })
            ->when(!empty($categoryIds) || !empty($authorIds), function ($query) use ($categoryIds, $authorIds) {
                $query->where(function ($subQuery) use ($categoryIds, $authorIds) {
                    if (!empty($categoryIds)) {
                        $subQuery->whereIn('category_id', $categoryIds);
                    }
                    if (!empty($authorIds)) {
                        $subQuery->orWhereIn('author_id', $authorIds);
                    }
                });
            })
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return $articles;
    }
    public function getSources(){
        $sources = Source::all();
        return response()->json([
            'status' => 'success',
            'data' => $sources,
        ], 200);
    }
    public function getCategories(){
        $categories = Category::all();
        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ], 200);
    }
    public function getCategoriesBySourceId(Request $request){
        $source_id = $request->source_id;

        $source = Source::find($source_id);
        $categories = $source->categories;
        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ], 200);
    }
    public function getAuthors(Request $request){
        $source_id = $request->source_id;

        $source = Source::find($source_id);
        $authors = $source->authors;
        return response()->json([
            'status' => 'success',
            'data' => $authors,
        ], 200);
    }
    private function getStartAndEndDate($selected_date){
        $startDate;
        $endDate;
        if($selected_date === 'Today'){
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        }
        elseif($selected_date === 'This Week'){
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        }
        else{
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }
        return ["startDate" => $startDate, "endDate" => $endDate];
    }
}
