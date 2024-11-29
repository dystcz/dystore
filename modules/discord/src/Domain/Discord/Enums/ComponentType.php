<?php

namespace Modules\DystoreDiscord\Domain\Discord\Enums;

enum ComponentType: int
{
    case ACTION_ROW = 1;
    case BUTTON = 2;
    case STRING_SELECT = 3;
    case TEXT_INPUT = 4;
    case USER_SELECT = 5;
    case ROLE_SELECT = 6;
    case MENTIONABLE_SELECT = 7;
    case CHANNEL_SELECT = 8;
}
