<?php

namespace ClawRock\Slack\Common\Enum;

use MabeEnum\Enum;

/**
 * Class MenuSource
 * @package ClawRock\Slack\Common\Enum
 */
class MenuSource extends Enum
{
    const USERS = 'users';
    const CONVERSATIONS = 'conversations';
    const CHANNELS = 'channels';
    const EXTERNAL = 'external';
    const STATIC_SOURCE = 'static';
}
