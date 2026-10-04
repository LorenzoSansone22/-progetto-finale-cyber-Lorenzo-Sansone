<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;

class HttpService
{
    protected $client;
    protected $allowedDomains = ['internal.finance', 'newsapi.org'];
    protected $allowedProtocols = ['http', 'https'];
    protected $refererHeader;

    public function __construct()
    {
        $this->refererHeader = config('app.url');
        $this->client = new Client();
    }

    public function getRequest($url)
    {
        $parsedUrl = parse_url($url);

        if (!isset($parsedUrl['scheme']) || !in_array($parsedUrl['scheme'], $this->allowedProtocols, true)) {
            return 'Protocol not allowed';
        }

        if (!isset($parsedUrl['host']) || !in_array($parsedUrl['host'], $this->allowedDomains, true)) {
            return 'Domain not allowed';
        }

        // Solo gli admin possono richiedere dati a internal.finance
        if ($parsedUrl['host'] === 'internal.finance') {
            if (!Auth::check() || !Auth::user()->is_admin) {
                return json_encode(['error' => 'Forbidden']);
            }
        }

        $options['headers'] = ['Referer' => $this->refererHeader];

        try {
            $response = $this->client->request('GET', $url, $options);
            return $response->getBody()->getContents();
        } catch (RequestException $e) {
            return 'Something went wrong: ' . $e->getMessage();
        }
    }
}