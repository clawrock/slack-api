<?php

namespace ClawRock\Slack\Common\Enum;

use MabeEnum\Enum;

/**
 * Class MenuSource
 * @package ClawRock\Slack\Common\Enum
 */
class ActionType extends Enum
{
    const MENU = 'select';
    const BUTTON = 'button';
}
