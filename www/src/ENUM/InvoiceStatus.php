<?php

namespace App\ENUM;

enum InvoiceStatus: string
{
    case COMPLETE = 'complete';
    case ERROR = 'error';
    case PENDING = 'pending';
}
