<?php

namespace App\Deploy\Steps;

use Symfony\Component\HttpFoundation\Request;

interface Step
{
    function execute(string $owner, string $repo, Request $request, array $options): void;
    function validate(array $options): bool;
}
