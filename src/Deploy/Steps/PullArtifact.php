<?php

namespace App\Deploy\Steps;

use App\Deploy\AuthenticationToken;
use App\Http\Github\Github;
use App\Repository\DeployConfigurationRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use ZipArchive;

readonly class PullArtifact implements Step
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
     * @param array{artifactId: int} $options
     * @return void
     * @throws Exception
     */
    public function execute(string $owner, string $repo, Request $request, array $options): void
    {
        $token = $this->getAuthenticationToken($request);
        $configuration = $this->deployConfigurationRepository->findOneByGitRepository($owner, $repo);

        if (!$configuration)
            throw new AuthenticationException();

        $artifactId = $options['artifactId'];
        $artifact = $this->github->withToken($token)
            ->repo($owner, $repo)
            ->artifact($artifactId)
            ->download();

        $projectRoot = $this->appKernel->getProjectDir();
        $zipPath = "$projectRoot/var/temp/{$owner}_{$repo}_$artifactId.zip";

        file_put_contents($zipPath, $artifact);

        $zip = new ZipArchive();

        if (!$zip->open($zipPath))
            throw new Exception('Failed to open zip archive');

        $serverRoot = dirname($projectRoot);
        $deployPath = $configuration->getDeployPath();

        $zip->extractTo("$serverRoot/$deployPath/staging");
        $zip->close();
    }

    public function validate(array $options): bool
    {
        return key_exists('artifactId', $options) && is_int($options['artifactId']);
    }
}
