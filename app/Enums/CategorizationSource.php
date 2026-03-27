<?php

namespace App\Enums;

enum CategorizationSource: string
{
    case System = 'system';
    case User   = 'user';
    case Ai     = 'ai';
}
