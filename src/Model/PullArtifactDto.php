<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class PullArtifactDto
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $artifactId,

        #[Assert\NotBlank]
        public readonly string $destinationPath
    ) { }
}
