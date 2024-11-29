<?php

namespace Modules\DystoreDiscord\Domain\Discord\Enums;

enum ButtonStyle: int
{
    case PRIMARY = 1;
    case SECONDARY = 2;
    case SUCCESS = 3;
    case DANGER = 4;
    case LINK = 5;
}
