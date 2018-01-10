<?php

namespace ClawRock\Slack\Logic;

use ClawRock\Slack\Logic\RequestSender\RequestOptions;
use ClawRock\Slack\Logic\RequestSender\RequestSenderInterface;

class Snippet
{
    /**
     * @var RequestSenderInterface
     */
    protected $requestSender;

    /**
     * @var RequestOptions
     */
    protected $requestOptions;

    public function __construct(RequestSenderInterface $requestSender, RequestOptions $requestOptions)
    {
        $this->requestOptions = $requestOptions;
        $this->requestSender = $requestSender;
    }

    /**
     * @return RequestOptions
     */
    public function getRequestOptions()
    {
        return $this->requestOptions;
    }

    /**
     * @return bool
     */
    public function send()
    {
        return $this->requestSender->send($this->requestOptions);
    }
}
