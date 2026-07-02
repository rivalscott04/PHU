<?php

namespace App\Enums;

enum FindingSeverity: string
{
    case Minor = 'MINOR';
    case Major = 'MAJOR';
    case Critical = 'CRITICAL';
}
