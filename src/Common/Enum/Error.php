<?php

namespace ClawRock\Slack\Common\Enum;

use MabeEnum\Enum;

class Error extends Enum
{
    const NOT_ALLOWED = 'You are not allowed to call this command.';
    const COMMAND_NOT_FOUND = 'I don\'t know this command.';
    const NO_PARAMETER_MATCH = 'I don\'t know this parameter';
}
