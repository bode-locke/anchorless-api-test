<?php

namespace App\Enums;

enum FileCategory: string
{
    case Identity = 'identity';
    case Financial = 'financial';
    case Supporting = 'supporting';
}
