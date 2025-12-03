<?php

namespace App\Deploy;

use App\Deploy\Steps\PreStaging;
use App\Deploy\Steps\PullArtifact;
use App\Deploy\Steps\Step;
use App\Http\Github\Github;
use App\Repository\DeployConfigurationRepository;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\KernelInterface;

readonly class StepFactory
{
    function __construct(
        private Github $github,
        private DeployConfigurationRepository $deployConfigurationRepository,
        private KernelInterface $appKernel
    ) { }

    public function getStep(string $step): Step
    {
        switch ($step)
        {
            case 'pre-staging':
                return new PreStaging($this->github, $this->deployConfigurationRepository, $this->appKernel);
            case 'pull-artifact':
                return new PullArtifact($this->github, $this->deployConfigurationRepository, $this->appKernel);
        }

        throw new InvalidArgumentException("Step $step is not supported");
    }
}
