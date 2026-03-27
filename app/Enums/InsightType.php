<?php

namespace App\Enums;

enum InsightType: string
{
    case Alert = 'alert';
    case Suggestion = 'suggestion';
    case Risk = 'risk';
}
