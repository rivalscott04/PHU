<?php

namespace App\Enums;

enum InspectionStatus: string
{
    case Draft = 'DRAFT';
    case Scheduled = 'SCHEDULED';
    case OnProgress = 'ON_PROGRESS';
    case WaitingFollowup = 'WAITING_FOLLOWUP';
    case FollowupUploaded = 'FOLLOWUP_UPLOADED';
    case Verified = 'VERIFIED';
    case Closed = 'CLOSED';
    case Cancelled = 'CANCELLED';
}
