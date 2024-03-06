<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Chat
{
    public function __construct(private HttpClientInterface $httpClient){

    }
    public function sendMessage($text)
    {
        $timeout = 120;
        $response = $this->httpClient->request('POST', 'http://10.9.64.10:1234/v1/chat/completions', [
            'headers' => [
                'Content-Type' => 'application/json',
                'timeout' => $timeout,
            ],
            'json' => [
                'messages' => [

                    ['role' => 'user', 'content' => $text],

                ],
                'temperature' => 0.7,
                'max_tokens' => -1,
                'stream' => false,
            ],
        ]);

        $iaResponse = $response->toArray();

        return $iaResponse["choices"][0]["message"];
    }

    public function sendMessagePdf($pdfText,$text)
    {
        $timeout = 240;
        $response = $this->httpClient->request('POST', 'http://10.9.64.10:1234/v1/chat/completions', [
            'headers' => [
                'Content-Type' => 'application/json',
                'timeout' => $timeout,
            ],
            'json' => [
                'messages' => [
                    ['role' => 'user', 'content' => $pdfText,"\n",$text],
                ],
                'temperature' => 0.7,
                'max_tokens' => -1,
                'stream' => false,
            ]
        ]);

        $iaResponse = $response->toArray();

        return $iaResponse["choices"][0]["message"];
    }
}