<?php

namespace App\Enums;

enum InspectionType: string
{
    case Routine = 'ROUTINE';
    case SpotCheck = 'SPOT_CHECK';
    case ComplaintBased = 'COMPLAINT_BASED';
    case Special = 'SPECIAL';
}
