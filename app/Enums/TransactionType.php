<?php

namespace App\Enums;

enum TransactionType: string
{
    case Entrada = 'entrada';
    case Saida = 'saida';
}
