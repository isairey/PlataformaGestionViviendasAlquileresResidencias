<?php

namespace App\Enums;

enum AnnouncementType: string
{
    case SYSTEM = 'system';
    case PROPERTY = 'property';
    case ROOM = 'room';
}
