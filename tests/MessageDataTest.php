<?php

namespace ClawRock\Slack\Test;

use ClawRock\Slack\Logic\MessageData;

class MessageDataTest extends \PHPUnit_Framework_TestCase
{
    public function test_converting_to_message()
    {
        $messageData = new MessageData();
        $message     = $messageData->setArgument('text', 'text')->toMessage(getenv('EMPTY_IP'));
        $this->assertArrayHasKey('text', $message->getMessageData()->getContent());
    }

    public function test_converting_to_response()
    {
        $messageData = new MessageData();
        $response    = $messageData->setArgument('text', 'text')->toResponse();
        $this->assertArrayHasKey('text', $response->getMessageData()->getContent());
    }

}
