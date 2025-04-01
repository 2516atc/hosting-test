<?php

namespace App\Controller;

use App\Model\PullArtifactDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/deploy', 'deploy')]
class DeployController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/{owner}/{repo}/pull_artifact', ':pull_artifact')]
    public function pullArtifact(
        string $owner,
        string $repo,
        #[MapRequestPayload] PullArtifactDto $pullArtifactDto,
        HttpClientInterface $githubClient,
        Request $request
    ): Response
    {
        $response = $githubClient->request(
            Request::METHOD_GET,
            "/repos/$owner/$repo/actions/artifacts/$pullArtifactDto->artifactId/zip",
            [
                'headers' => [
                    'Authorization' => $request->headers->get('Authorization')
                ]
            ]
        );

        dd($response);
    }
}
