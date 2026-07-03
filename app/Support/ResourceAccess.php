<?php

namespace App\Support;

final class ResourceAccess
{
    /**
     * Deny access to a specific resource without revealing that it exists.
     */
    public static function denyUnless(bool $allowed): void
    {
        abort_unless($allowed, 404);
    }
}
