<?php

namespace ClawRock\Slack\Logic\Command;

use ClawRock\Slack\Fluent\Response\MessageDataBuilderInterface;
use ClawRock\Slack\Logic\Request\RequestInterface;

interface CommandInterface
{
    /**
     * @param RequestInterface $request
     * @return Command
     */
    public function __invoke(RequestInterface $request, MessageDataBuilderInterface $response);

    /**
     * @param $token
     * @return Container
     */
    public function on($value);
}
