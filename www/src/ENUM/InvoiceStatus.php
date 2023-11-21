<?php

namespace App\Entity\ENUM;

enum InvoiceStatus: string
{
    case COMPLETE = 'complete';
    case ERROR = 'error';
}
