<?php

namespace App\Enums;

enum InstallmentItemStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Overdue = 'overdue';
}
