<?php

namespace App\Http;

use App\Enum\SessionAttributeKey;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Session
{
    private SessionInterface $session;

    function __construct(Request $request)
    {
        $this->session = $request->getSession();
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request);
    }

    public function get(SessionAttributeKey $key): mixed
    {
        return $this->session->get($key->value);
    }

    public function set(SessionAttributeKey $key, $value): void
    {
        $this->session->set($key->value, $value);
    }

    public function remove(SessionAttributeKey $key): void
    {
        $this->session->remove($key->value);
    }
}
