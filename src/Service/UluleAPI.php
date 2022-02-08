<?php

namespace App\Service;


use Symfony\Contracts\HttpClient\HttpClientInterface;



class UluleAPI
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchProjectInfo(): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.ulule.com/v1/projects/141629  ', [
                // these values are automatically encoded before including them in the URL
                'query' => [
                    'lang' => 'fr',
                ],
            ]
        );

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'

        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]
        dd($content);

        return $content;
    }
}