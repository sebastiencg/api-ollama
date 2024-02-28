<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Askia
{

    public function __construct(private HttpClientInterface $httpClient) {}

    public function sendPrompt(array $messages) {
        $formattedMessages = [];

        foreach ($messages as $message) {
            $formattedMessages[] = ['role' => 'user', 'content' => $message['content']];
        }

        $response = $this->httpClient->request('POST', 'http://10.9.64.10:1234/v1/chat/completions', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'messages' => $formattedMessages,
                'temperature' => 0.7,
                'max_tokens' => -1,
                'stream' => false,
            ],
        ]);

        $iaResponse = $response->toArray();

        return $iaResponse["choices"][0]["message"];
    }
}