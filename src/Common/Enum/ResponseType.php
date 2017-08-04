<?php

namespace ClawRock\Slack\Common\Enum;

use MabeEnum\Enum;

class ResponseType extends Enum
{
    const EPHEMERAL = 'ephemeral';
    const IN_CHANNEL = 'in_channel';
}
