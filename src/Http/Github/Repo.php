<?php

namespace App\Http\Github;

readonly class Repo extends AbstractApi
{
    function __construct(
        private Github $github,
        public ?string $owner = null,
        public ?string $repo = null
    )
    {
        parent::__construct($this->github);
    }

    public function actionRun(int $id): ActionRun
    {
        return new ActionRun($this->github, $this, $id);
    }

    public function artifact(int $id): Artifact
    {
        return new Artifact($this->github, $this, $id);
    }
}
