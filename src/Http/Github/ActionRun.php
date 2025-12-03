<?php

namespace App\Http\Github;

readonly class ActionRun extends AbstractApi
{
    function __construct(
        private Github $github,
        private Repo $repo,
        private ?int $id = null
    )
    {
        parent::__construct($this->github);
    }

    public function get(): array
    {
        $owner = $this->repo->owner;
        $repo = $this->repo->repo;

        return json_decode($this->sendRequest("/repos/$owner/$repo/actions/runs/$this->id"), true);
    }
}
