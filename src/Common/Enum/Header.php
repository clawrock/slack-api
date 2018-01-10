<?php

namespace ClawRock\Slack\Common\Enum;

use MabeEnum\Enum;

class Header extends Enum
{
    const JSON = 'Content-type: application/json';
    const URL_UNENCODED = 'Content-Type: application/x-www-form-urlencoded';
    const MULTIPART = 'Content-Type: multipart/form-data';
}
