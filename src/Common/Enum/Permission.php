<?php

namespace ClawRock\Slack\Common\Enum;

use MabeEnum\Enum;

class Permission extends Enum
{
    const DENY_ALL = 'denyall';
    const ALLOW_ALL = 'allowall';
}
