<?php

namespace App\Enums;

enum BillStatus: string
{
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
}
