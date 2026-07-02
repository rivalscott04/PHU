<?php

namespace App\Enums;

enum FindingStatus: string
{
    case Open = 'OPEN';
    case WaitingResponse = 'WAITING_RESPONSE';
    case FollowupUploaded = 'FOLLOWUP_UPLOADED';
    case RevisionRequired = 'REVISION_REQUIRED';
    case Verified = 'VERIFIED';
    case Closed = 'CLOSED';
}
