<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
}
