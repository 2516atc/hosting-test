<?php

namespace App\Http\Github;

readonly class Artifact extends AbstractApi
{
    function __construct(
        private Github $github,
        private Repo $repo,
        private ?int $id = null
    )
    {
        parent::__construct($this->github);
    }

    public function download(): string
    {
        $owner = $this->repo->owner;
        $repo = $this->repo->repo;

        return $this->sendRequest("/repos/$owner/$repo/actions/artifacts/$this->id/zip");
    }
}
