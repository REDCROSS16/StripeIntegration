<?php

namespace App\Entity\ENUM;

enum CardStatus: string
{
    case ACTIVE = 'active';
    case DISABLE = 'disable';
}
