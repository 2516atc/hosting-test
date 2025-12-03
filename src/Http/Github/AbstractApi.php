<?php

namespace App\Http\Github;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

abstract readonly class AbstractApi
{
    function __construct(private Github $github) { }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function sendRequest(string $endpoint): string
    {
        $options = [
            'base_uri' => 'https://api.github.com/',
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json'
            ]
        ];

        if ($this->github->token)
        {
            $options['headers']['Authorization'] = 'Bearer ' . $this->github->token;
        }

        return $this->github->httpClient
            ->request(Request::METHOD_GET, $endpoint, $options)
            ->getContent();
    }
}
