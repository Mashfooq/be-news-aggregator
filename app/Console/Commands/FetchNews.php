<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use Carbon\Carbon;

class FetchNews extends Command
{
    protected $signature = 'news:fetch';
    protected $description = 'Fetch news articles from external sources';

    public const SOURCE_NEWS_API = 'NewsAPI';
    public const SOURCE_THE_GUARDIAN = 'The Guardian';
    public const SOURCE_NYT = 'New York Times';

    public const US_COUNTRY = 'us';

    public $storedCategories = null;
    public $storedSources = null;

    // List of models to use for categorizing news articles
    private $models = ['anthropic/claude-3.7-sonnet:beta', 'mistralai/mistral-7b-instruct'];

    // List the API urls from where you want to fetch the news articles
    private $apiUrls = [];

    private $openRouterApiKey = null;

    public function __construct()
    {
        parent::__construct();

        $this->apiUrls = [
            self::SOURCE_NEWS_API => "https://newsapi.org/v2/top-headlines?country=" . self::US_COUNTRY . "&apiKey=" . env('NEWS_API_KEY'),
            self::SOURCE_THE_GUARDIAN => "https://content.guardianapis.com/search?api-key=" . env('GUARDIAN_API_KEY') . "&show-fields=thumbnail",
            self::SOURCE_NYT => "https://api.nytimes.com/svc/topstories/v2/home.json?api-key=" . env('NYTIMES_API_KEY'),
        ];
    }

    public function handle()
    {
        $this->info(string: 'Task is aborted...');

        // TODO: Remove this.
        return;

        $this->info(string: 'Fetching news articles...');

        $this->openRouterApiKey = env('OPENROUTER_API_KEY');

        // Fetch all categories and sources from the database
        $this->storedCategories = Category::pluck('id', 'name')->toArray();
        $this->storedSources = Source::pluck('id', 'name')->toArray();

        $this->fetchNewsAPI();
        $this->fetchGuardianAPI();
        $this->fetchNYTimesAPI();

        $this->info('News articles fetched successfully!');
    }

    private function fetchNewsAPI()
    {
        if (empty(env('NEWS_API_KEY'))) {
            $this->info("Unable to get the value of NEWS_API_KEY");
        }

        $this->info('Fetching news articles from NewsAPI...');

        try {
            $response = $this->getResponse($this->apiUrls[self::SOURCE_NEWS_API]);
        } catch (Exception $exception) {
            $this->error('Failed to fetch news articles from NewsAPI. Exception caught: ' . $exception->getMessage());
            return;
        }
    
        if ($response && $response->successful()) {
            $articles = collect($response->json()['articles'])->map(function ($item) {
                // Ensure source name is present
                $sourceName = $item['source']['name'] ?? 'Unknown Source';
    
                // Ensure source_id is correctly fetched
                $sourceId = $this->getSourceId($sourceName);
                if (!$sourceId) {
                    $this->error("Failed to get source_id for: {$sourceName}");
                }
    
                // Get category ID
                $category = $this->getCategory($item['title'], $item['description']);
                $categoryId = $this->getCategoryId($category);
    
                return [
                    'title' => $item['title'],
                    'content' => $item['description'] ?? null,
                    'url' => $item['url'],
                    'image_url' => $item['urlToImage'] ?? null,
                    'source_id' => $sourceId,
                    'category_id' => $categoryId,
                    'published_at' => isset($item['publishedAt']) ? Carbon::parse($item['publishedAt']) : now(),
                ];
            })->toArray();
    
            $this->saveArticles($articles);
        }

        $this->info('News articles fetched successfully from NewsAPI!');
    }

    private function fetchGuardianAPI()
    {
        if (empty(env('GUARDIAN_API_KEY'))) {
            $this->info("Unable to get the value of GUARDIAN_API_KEY");
        }

        $this->info('Fetching news articles from The Guardian...');

        try {
            $response = $this->getResponse($this->apiUrls[self::SOURCE_THE_GUARDIAN]);
        } catch (Exception $exception) {
            $this->error('Failed to fetch news articles from NewsAPI. Exception caught: ' . $exception->getMessage());
            return;
        }

        if ($response && $response->successful()) {
            $articles = collect($response->json()['response']['results'])->map(function ($item) {
                // Ensure source_id is correctly fetched
                $sourceId = $this->getSourceId(self::SOURCE_THE_GUARDIAN);
                if (!$sourceId) {
                    $this->error("Failed to get source_id for: " . self::SOURCE_THE_GUARDIAN);
                }

                // Get category ID
                $category = $this->getCategory($item['webTitle']);
                $categoryId = $this->getCategoryId($category);

                return [
                    'title' => $item['webTitle'],
                    'content' => null,
                    'url' => $item['webUrl'],
                    'image_url' => $item['fields'][0]['thumbnail'] ?? null,
                    'source_id' => $sourceId,
                    'category_id' => $categoryId,
                    'published_at' => isset($item['webPublicationDate']) ? Carbon::parse($item['webPublicationDate']) : now(),
                ];
            })->toArray();

            $this->saveArticles($articles);
        }

        $this->info('News articles fetched successfully from The Guardian!');
    }

    private function fetchNYTimesAPI()
    {
        if (empty(env('NYTIMES_API_KEY'))) {
            $this->info("Unable to get the value of NYTIMES_API_KEY");
        }

        $this->info('Fetching news articles from New York Times...');

        try {
            $response = $this->getResponse($this->apiUrls[self::SOURCE_NYT]);
        } catch (Exception $exception) {
            $this->error('Failed to fetch news articles from NewsAPI. Exception caught: ' . $exception->getMessage());
            return;
        }

        if ($response && $response->successful()) {
            $articles = collect($response->json()['results'])->map(function ($item) {
                // Ensure source_id is correctly fetched
                $sourceId = $this->getSourceId(self::SOURCE_NYT);
                if (!$sourceId) {
                    $this->error("Failed to get source_id for: " . self::SOURCE_NYT);
                }

                // Get category ID
                $category = $this->getCategory($item['title'], $item['abstract']);
                $categoryId = $this->getCategoryId($category);

                // Ensure source_id is correctly fetched
                return [
                    'title' => $item['title'],
                    'content' => $item['abstract'] ?? null,
                    'url' => $item['url'],
                    'image_url' => $item['multimedia'][0]['url'] ?? null,
                    'source_id' => $sourceId,
                    'category_id' => $categoryId,
                    'published_at' => isset($item['published_date']) ? Carbon::parse($item['published_date']) : now(),
                ];
            });

            $this->saveArticles($articles);
        }

        $this->info('News articles fetched successfully from New York Times!');
    }

    private function saveArticles($articles)
    {
        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['url' => $article['url']],  // Prevent duplicate entries
                $article
            );
        }
    }

    // Creatae a private function getResponse($url) to handle the HTTP request, by trying max 3 times.
    // If the request is successful, return the response, otherwise return null.
    public function getResponse($url)
    {
        $attempts = 0;

        while ($attempts < 3) {
            try {
                $response = Http::get($url);
            } catch (Exception $exception) {
                $this->error('Failed to fetch news articles, Error messages: ' . $exception->getMessage());
                return null;
            }

            if ($response->successful()) {
                return $response;
            }

            $attempts++;
        }

        return null;
    }

    private function getCategory($title, $description = null)
    {
        $cacheKey = 'news_category:' . md5($title . ($description ?? ''));

        // Check if category is already cached
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $content = "Title: {$title}. Description: " . ($description ?? 'No description available.');

        // Try each model until we get a successful response
        foreach ($this->models as $model) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer $this->openRouterApiKey",
                    'Content-Type' => 'application/json',
                ])
                    ->timeout(60)
                    ->retry(3, 2000) // Retry 3 times with a 2-second delay
                    ->post('https://openrouter.ai/api/v1/chat/completions', [
                        'model' => $model,
                        'messages' => [
                            ['role' => 'system', 'content' => 'Categorize the following news article into categories like Technology, Politics, Sports, etc. Return only the category name.'],
                            ['role' => 'user', 'content' => $content]
                        ],
                        'temperature' => 0.5,
                        'max_tokens' => 10
                    ]);

                if ($response->successful()) {
                    $category = trim($response->json()['choices'][0]['message']['content'] ?? 'Unknown');

                    // Store the result in cache for 24 hours
                    Cache::put($cacheKey, $category, now()->addMinutes(1440));

                    return $category;
                }

                $this->error("Model $model failed with response: " . json_encode($response->json()));
            } catch (\Illuminate\Http\Client\RequestException $exception) {
                $this->error("OpenRouter API Error ({$model}): " . $exception->getMessage());
            }
        }

        return 'Unknown'; // Default fallback category
    }

    private function getCategoryId($category)
    {
        if (isset($this->storedCategories[$category])) {
            return $this->storedCategories[$category];
        }

        $categoryModel = Category::firstOrCreate(['name' => $category]);
        $this->storedCategories[$category] = $categoryModel->id;

        return $categoryModel->id;
    }

    private function getSourceId($source)
    {
        // Ensure we have a valid source name
        $source = trim($source ?? 'Unknown Source');
    
        // Check if we already stored this source in memory
        if (isset($this->storedSources[$source])) {
            return $this->storedSources[$source];
        }
    
        // If not found, create or fetch the source
        $sourceModel = Source::firstOrCreate(['name' => $source]);
    
        // Store in memory for future use
        $this->storedSources[$source] = $sourceModel->id;
    
        return $sourceModel->id;
    }
}
