<?php

namespace App\Entity\ENUM;

enum Roles: string
{
    case ROLE_ADMIN = 'ROLE_ADMIN';
    case ROLE_USER = 'ROLE_USER';
    case ROLE_MERCHANT = 'ROLE_MERCHANT';
}