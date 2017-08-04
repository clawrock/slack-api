<?php

namespace ClawRock\Slack\Common\Enum;

use MabeEnum\Enum;

class RequestType extends Enum
{
    const SLASH_COMMAND = 'slash-command';
    const INTERACTIVE_COMMAND = 'interactive-command';
}
