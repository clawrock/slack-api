<?php

namespace ClawRock\Slack\Logic\Command;

use ClawRock\Slack\Common\Enum\RequestType;

interface DispatcherCommandInterface extends CommandInterface
{
    /**
     * @return RequestType
     */
    public function getAllowedRequestType();

    /**
     * @return string
     */
    public function getToken();
}
