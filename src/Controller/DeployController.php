<?php

namespace App\Controller;

use App\Deploy\StepFactory;
use App\Deploy\Steps\PullArtifact;
use App\Model\DeployStepDto;
use App\Model\PullArtifactDto;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/deploy', 'deploy')]
class DeployController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/{owner}/{repo}/pull_artifact', ':pull_artifact')]
    public function pullArtifact(
        string $owner,
        string $repo,
        #[MapRequestPayload] PullArtifactDto $pullArtifactDto,
        Request $request,
        PullArtifact $pullArtifact
    ): Response
    {
        $pullArtifact->execute($owner, $repo, $pullArtifactDto->artifactId, $request);

        return new Response();
    }

    #[Route('/{owner}/{repo}/step', ':step', methods: ['PUT'])]
    public function step(
        string $owner,
        string $repo,
        #[MapRequestPayload] DeployStepDto $deployStepDto,
        Request $request,
        StepFactory $stepFactory
    ): Response
    {
        $step = $stepFactory->getStep($deployStepDto->stepName);

        if (!$step->validate($deployStepDto->options))
            throw new InvalidArgumentException();

        $step->execute($owner, $repo, $request, $deployStepDto->options);

        return new Response();
    }
}
