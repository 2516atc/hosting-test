<?php

namespace App\Http\Github;

readonly class User extends AbstractApi
{
    function __construct(Github $github)
    {
        parent::__construct($github);
    }

    public function currentUser()
    {
        return json_decode($this->sendRequest('/user'), true);
    }
}
