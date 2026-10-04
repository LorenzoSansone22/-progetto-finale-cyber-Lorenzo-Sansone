<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\HttpService;

class LatestNews extends Component
{
    public $selectedApi;
    public $news;
    protected $httpService;

    public function __construct()
    {
        $this->httpService = app(HttpService::class);
    }

    public function fetchNews()
    {
        $allowedCountries = ['it', 'gb', 'us'];

        if (!in_array($this->selectedApi, $allowedCountries, true)) {
            $this->news = ['error' => 'Selezione non valida'];
            return;
        }

        $apiKey = config('services.newsapi.api_key');
        $url = "https://newsapi.org/v2/top-headlines?country={$this->selectedApi}&apiKey={$apiKey}";

        $response = $this->httpService->getRequest($url);
        $this->news = json_decode($response, true);

        if (!is_array($this->news)) {
            $this->news = ['error' => 'Impossibile recuperare le news'];
        }
    }

    public function render()
    {
        return view('livewire.latest-news');
    }
}