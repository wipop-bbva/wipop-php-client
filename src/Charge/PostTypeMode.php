<?php

declare(strict_types=1);

namespace Wipop\Charge;

enum PostTypeMode: string
{
    case INTERNET = 'INTERNET';
    case RECURRENT = 'RECURRENT';
}
