<?php

namespace App\Deploy\Steps;

use App\Deploy\AuthenticationToken;
use App\Http\Github\Github;
use App\Repository\DeployConfigurationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

readonly class PreStaging implements Step
{
    use AuthenticationToken;

    function __construct(
        private Github $github,
        private DeployConfigurationRepository $deployConfigurationRepository,
        private KernelInterface $appKernel
    ) { }

    /**
     * @param string $owner
     * @param string $repo
     * @param Request $request
     * @param array{runId: int} $options
     * @return void
     */
    public function execute(string $owner, string $repo, Request $request, array $options): void
    {
        $token = $this->getAuthenticationToken($request);
        $configuration = $this->deployConfigurationRepository->findOneByGitRepository($owner, $repo);

        if (!$configuration)
            throw new AuthenticationException();

        $actionRun = $this->github->withToken($token)
            ->repo($owner, $repo)
            ->actionRun($options['runId'])
            ->get();

        if ($actionRun['status'] !== 'in_progress')
            throw new AccessDeniedException();

        $projectRoot = $this->appKernel->getProjectDir();
        $serverRoot = dirname($projectRoot);
        $deployPath = $configuration->getDeployPath();

        if (is_dir("$serverRoot/$deployPath/staging"))
        {
            if (!is_dir("$serverRoot/$deployPath/delete"))
                mkdir("$serverRoot/$deployPath/delete");

            $time = time();
            rename("$serverRoot/$deployPath/staging", "$serverRoot/$deployPath/delete/staging-$time");
        }

        mkdir("$serverRoot/$deployPath/staging");
    }

    public function validate(array $options): bool
    {
        return key_exists('runId', $options) && is_int($options['runId']);
    }
}
