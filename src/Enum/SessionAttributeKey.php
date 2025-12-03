<?php

namespace App\Enum;

enum SessionAttributeKey: string
{
    case AUTH_STATE = 'auth_state';
    case GITHUB_LOGIN = 'github_login';
    case GITHUB_USER_ID = 'github_user_id';
}
