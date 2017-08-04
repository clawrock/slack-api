<?php

namespace ClawRock\Slack\Test\RequestSender;

use ClawRock\Slack\Common\Enum\Header;
use ClawRock\Slack\Common\Enum\RequestMethod;
use ClawRock\Slack\Logic\MessageData;
use ClawRock\Slack\Logic\RequestSender\RequestOptions;
use ClawRock\Slack\Logic\RequestSender\StreamRequestSender;

class RequestSenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_invalid_url_throws_exception()
    {
        $slackMessage = new StreamRequestSender('invalid url');
    }

    public function test_return_false_if_wrong_url()
    {
        $requestOptions = new RequestOptions(Header::JSON(), json_encode(new MessageData()));

        $requestOptions->setMethod(RequestMethod::POST());

        $requestSender = new StreamRequestSender(getenv('EMPTY_IP'));

        $requestSender->send($requestOptions);

        $this->assertFalse($requestSender->send($requestOptions));
    }
}
