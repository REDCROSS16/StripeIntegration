<?php

namespace App\ENUM;

enum InvoiceStatus: int
{
    case COMPLETE = 1;
    case ERROR = 2;
    case PENDING = 3;
    case SUBSCRIBED = 4;
}
