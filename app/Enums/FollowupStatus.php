<?php

namespace App\Enums;

enum FollowupStatus: string
{
    case Submitted = 'SUBMITTED';
    case Pending = 'PENDING';
    case RevisionRequired = 'REVISION_REQUIRED';
    case Verified = 'VERIFIED';
    case Rejected = 'REJECTED';
    case Closed = 'CLOSED';
}
