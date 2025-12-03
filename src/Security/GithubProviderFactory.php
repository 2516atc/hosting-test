<?php

namespace App\Security;

use League\OAuth2\Client\Provider\Github;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class GithubProviderFactory
{
    function __construct(
        private string $githubClientId,
        private string $githubClientSecret,
        private string $routeName,
        private UrlGeneratorInterface $urlGenerator
    ) { }

    public function getProvider(): Github
    {
        return new Github([
            'clientId' => $this->githubClientId,
            'clientSecret' => $this->githubClientSecret,
            'redirectUri' => $this->urlGenerator->generate(
                $this->routeName,
                referenceType: UrlGeneratorInterface::ABSOLUTE_URL
            )
        ]);
    }
}
