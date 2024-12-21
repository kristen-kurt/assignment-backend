<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Article;
use App\Models\Source;
use App\Models\Author;
use App\Models\Category;
use Carbon\Carbon;

class FetchArticles extends Command
{
    protected $signature = 'app:fetch-articles';
    protected $description = 'Fetch data every hour';

    public function handle(){
        $articles = $this->getArticles();
        $this->storeArticles($articles);
        Log::info('FetchArticles command executed at ' . now());
    }
    private function getArticles()
    {
        $newsAPIs = [
            [
                 'source' => 'The Guradian',
                 'url' => 'https://content.guardianapis.com/search',
                 'apiKey' => config('app.guardian_api_key'),
                 'apiKeyParamName' => 'api-key'
             ],
             [
                 'source' => 'News API',
                 'url' => 'https://newsapi.org/v2/top-headlines',
                 'apiKey' => config('app.news_api_key'),
                 'apiKeyParamName' => 'apiKey'
             ],
             [
                'source' => 'The New York Times',
                'url' => 'https://api.nytimes.com/svc/mostpopular/v2/emailed/7.json',
                'apiKey' => config('app.newyork_times_api_key'),
                'apiKeyParamName' => 'api-key'
            ],
             
         ];
         $articles = [];
         foreach($newsAPIs as $newsAPI){
            
            $apiUrl = $newsAPI['url'];
            $apiKeyParamName = $newsAPI['apiKeyParamName'];
            $apiKey = $newsAPI['apiKey'];
            $siteName = $newsAPI['source'];

            $response = Http::get($apiUrl, 
            ($siteName !== 'The New York Times') ?
            [
                'country' => 'us',
                'category' => 'business',
                $apiKeyParamName => $apiKey,
                'page-size' => 10,
            ] :
            [
                $apiKeyParamName => $apiKey,
            ]
        );

            if ($response->successful()) {
                $data = $response->json()['response']['results']
                            ?? $response->json()['articles'] 
                            ?? $response->json()['results']
                            ?? [];

                array_push($articles, ...$data);
                $this->info('Articles fetched successfully!');
            } else {
                $this->error('Failed to fetch articles. Response: ' . $response->body());
            }
        }
        return $articles;
    }
    private function storeArticles($articles)
    {
        foreach ($articles as $article) {
            $normalizedData = $this->normalizeArticleData($article);
            if (!$normalizedData['category'] || !$normalizedData['site_name']) return;

            $source = Source::firstOrCreate(['name' => $normalizedData['site_name']]);
            $category = Category::firstOrCreate(
                ['name' => $normalizedData['category']],
                ['source_id' => $source->id]
            );
            $author = Author::firstOrCreate(
                ['name' => $normalizedData['author']],
                ['source_id' => $source->id]
            );
    
            Article::updateOrCreate(
                ['url' => $normalizedData['url']],
                array_merge($normalizedData, [
                    'category_id' => $category->id,
                    'source_id' => $source->id,
                    'author_id' => $author->id
                ])
            );
        }
    }
    private function normalizeArticleData(array $article): array
    {
        $site_name = "";
        if(isset($article['pillarName'])){
            $site_name = 'The Guradian';
        }elseif(isset($article['source']['name'])){
            $site_name = 'News API';
        }else{
            $site_name = 'The New York Times';
        }
        return [
            'image_url' => $article['urlToImage'] ?? $article['media'][0]['media-metadata'][2]['url'] ??null,
            'author' => $article['author'] ?? $article['byline'] ?? 'Unknown',
            'url' => $article['webUrl'] ?? $article['url'],
            'article_title' => $article['webTitle'] ?? $article['title'],
            'site_name' => $site_name,
            'category' => $article['sectionName'] ?? $article['source']['id'] ?? $article['section'] ?? null,
            'published_at' => isset($article['webPublicationDate']) || isset($article['publishedAt']) || isset($article['updated'])
                ? Carbon::parse($article['webPublicationDate'] ?? $article['publishedAt'] ?? $article['updated'])->format('Y-m-d H:i:s')
                : null,
        ];
    }
}
