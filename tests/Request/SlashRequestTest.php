<?php

namespace ClawRock\Slack\Test\Request;

use ClawRock\Slack\Logic\MessageData;
use ClawRock\Slack\Logic\Request\SlashRequest;
use ClawRock\Slack\Logic\Response\Response;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    private $requestArray = [
        'token' => 'DEFAULTTOKEN',
    ];

    private $slashRequest;

    public function setUp()
    {
        $this->slashRequest = new SlashRequest($this->requestArray);
    }

    public function test_get_request_properties()
    {
        $this->assertEquals('DEFAULTTOKEN', $this->slashRequest->getToken());
        $this->assertEquals($this->requestArray, $this->slashRequest->getRequestData());
    }

    public function test_empty_request_field_returns_empty_string()
    {
        $this->assertEquals('', $this->slashRequest->getChannelId());
    }

    public function test_setting_message_data()
    {
        $messageData = new MessageData();
        $messageData->setArgument('text', 'text');

        $response = new Response(new MessageData());
        $response->setMessageData($messageData);

        $this->assertEquals('{"text":"text"}', $response->__toString());

    }
}
