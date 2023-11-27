<?php

namespace App\ENUM;

enum PaymentStatus: string
{
    case ACCEPT = 'accept';
    case DECLINE = 'decline';
}
