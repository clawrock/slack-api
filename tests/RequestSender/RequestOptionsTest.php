<?php

namespace ClawRock\Slack\Test\RequestSender;

use ClawRock\Slack\Common\Enum\Header;
use ClawRock\Slack\Common\Enum\RequestMethod;
use ClawRock\Slack\Logic\MessageData;
use ClawRock\Slack\Logic\RequestSender\RequestOptions;

class RequestOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function test_setting_options()
    {
        $messageData = new MessageData();
        $messageData->setArgument('text', 'text');
        $requestOptions = new RequestOptions(Header::JSON(), json_encode(new MessageData()));
        $requestOptions->setContent($messageData);
        $requestOptions->setHeader(Header::JSON());
        $requestOptions->setMethod(RequestMethod::POST());

        $this->assertEquals('POST', $requestOptions->getMethod());
        $this->assertEquals($messageData, $requestOptions->getContent());
        $this->assertEquals(Header::JSON, $requestOptions->getHeader());
    }

    public function test_convert_to_array()
    {
        $messageData = new MessageData();
        $messageData->setArgument('text', 'text');
        $requestOptions = new RequestOptions(Header::JSON(), json_encode($messageData));
        $requestOptions->setHeader(Header::URL_UNENCODED());
        $requestOptions->setMethod(RequestMethod::POST());

        $this->assertEquals(
            [
                'http' => [
                    'header'  => Header::URL_UNENCODED,
                    'method'  => 'POST',
                    'content' => '{"text":"text"}'
                ]
            ],
            $requestOptions->toArray()
        );
    }
}
