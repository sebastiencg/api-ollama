<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Askia
{

    public function __construct(private HttpClientInterface $httpClient){}

    public function sendPrompt($prompt) {
        $response = $this->httpClient->request('POST', 'http://10.9.64.10:1234/v1/', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => -1,
                'stream' => false,
            ],
        ]);

        $iaResponse = $response->toArray();

        return $iaResponse["choices"][0]["message"];
    }
}