<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class DeployStepDto
{
    function __construct(
        #[Assert\NotBlank]
        public string $stepName,
        public array $options
    ) { }
}
