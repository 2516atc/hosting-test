<?php

namespace App\Http\Github;

use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class Github
{
    public string $token;

    function __construct(public HttpClientInterface $httpClient) { }

    public function user(): array
    {
        return new User($this)->currentUser();
    }

    public function repo(string $owner, string $repo): Repo
    {
        return new Repo($this, $owner, $repo);
    }

    public function withToken(string $token): Github
    {
        $client = new self($this->httpClient);
        $client->token = $token;

        return $client;
    }
}
