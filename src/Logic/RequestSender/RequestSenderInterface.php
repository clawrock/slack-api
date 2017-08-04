<?php

namespace ClawRock\Slack\Logic\RequestSender;

interface RequestSenderInterface
{
    /**
     * Sends request.
     *
     * @param RequestOptions $options Class containing headers, method and content
     *
     * @return bool Returns true if request was successful. Otherwise false
     */
    public function send(RequestOptions $options);
}
