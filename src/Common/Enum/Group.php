<?php

namespace ClawRock\Slack\Common\Enum;

use MabeEnum\Enum;

/**
 * Class Groups
 * @package ClawRock\Slack\Common\Enum
 */
class Group extends Enum
{
    const USER = 'user';
    const TEAM = 'team';
    const CHANNEL = 'channel';
}
