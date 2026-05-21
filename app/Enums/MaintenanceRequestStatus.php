<?php

namespace App\Enums;

enum MaintenanceRequestStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
    case REJECTED = 'rejected';
}
