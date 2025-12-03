<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

readonly class PullArtifactDto
{
    public function __construct(
        #[Assert\NotBlank]
        public int $artifactId
    ) { }
}
