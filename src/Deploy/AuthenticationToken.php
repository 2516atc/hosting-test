<?php

namespace App\Deploy;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

trait AuthenticationToken
{
    public function getAuthenticationToken(Request $request): string
    {
        if (!preg_match('/^Bearer (.+)/', $request->headers->get('Authorization'), $matches))
            throw new AuthenticationException();

        return $matches[1];
    }
}
