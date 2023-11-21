<?php

namespace App\Entity\ENUM;

enum PaymentStatus: string
{
    case ACCEPT = 'accept';
    case DECLINE = 'decline';
}
