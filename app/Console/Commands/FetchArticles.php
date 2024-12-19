<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Article;
use Carbon\Carbon;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch data every hour';

    /**
     * Execute the console command.
     */
    public function handle()
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
            
        ];
        foreach($newsAPIs as $newsAPI){
            $apiUrl = $newsAPI['url'];
            $apiKeyParamName = $newsAPI['apiKeyParamName'];
            $apiKey = $newsAPI['apiKey'];
            $siteName = $newsAPI['source'];

            $response = Http::get($apiUrl, [
                'country' => 'us',
                'category' => 'business',
                $apiKeyParamName => $apiKey,
                'page-size' => 10,
            ]);

            if ($response->successful()) {
                $articles = $response->json()['response']['results']
                            ?? $response->json()['articles'] 
                            ?? [];
    
                foreach ($articles as $articleData) {

                    $urlToImage = $articleData['urlToImage'] ?? null;
                    $category = $articleData['sectionName'] ?? null;
                    $url = $articleData['webUrl'] ?? $articleData['url'];
                    $articleTitle = $articleData['webTitle'] ?? $articleData['title'];

                    $originalPublishedDate = $articleData['webPublicationDate'] ?? $articleData['publishedAt']; // ISO 8601 format
                    $convertedPublishedDate = Carbon::parse($originalPublishedDate)->format('Y-m-d H:i:s');

                    Article::updateOrCreate(
                        ['url' => $url],
                        [
                            'site_name' => $siteName,
                            'article_title' => $articleTitle,
                            'category' => $category,
                            'url' => $url,
                            'image_url' => $urlToImage,
                            'published_at' => $convertedPublishedDate,
                        ]
                    );
                }
    
                $this->info('Articles fetched and stored successfully!');
            } else {
                $this->error('Failed to fetch articles. Response: ' . $response->body());
            }
        }
        Log::info('FetchArticles command executed at ' . now());
    }
}
