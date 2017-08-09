<?php

namespace ClawRock\Slack\Logic\Request;

use ClawRock\Slack\Common\Enum\RequestType;

interface RequestInterface
{
    /**
     * @return RequestType
     */
    public function getRequestType();

    /**
     * @param string|null $key
     * @return mixed
     */
    public function getRequestData($key = null);

    /**
     * @return string
     */
    public function getTeamId();

    /**
     * @return string
     */
    public function getUserId();

    /**
     * @return string
     */
    public function getChannelId();

    /**
     * @return string
     */
    public function getToken();

    /**
     * Returns value that has to match to run command's on($value) method.
     *
     * @return string
     */
    public function getCommandParameterString();

    /**
     * @return string
     */
    public function getResponseUrl();
}
