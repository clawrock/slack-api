<?php

namespace ClawRock\Slack\Test;

use ClawRock\Slack\Logic\Message;
use ClawRock\Slack\Logic\MessageData;
use ClawRock\Slack\Logic\RequestSender\StreamRequestSender;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function test_sending_message_returns_self_instance()
    {
        $requestSenderStub = $this->getMockBuilder('ClawRock\Slack\Logic\RequestSender\RequestSenderInterface')
            ->setMethods(array('send', '__construct'))
            ->getMock();

        $slackMessage = new Message($requestSenderStub);
        $this->assertInstanceOf('ClawRock\Slack\Logic\Message', $slackMessage->send(new MessageData()));
    }

    public function test_sending_text_message_returns_self_instance()
    {
        $requestSenderStub = $this->getMockBuilder('ClawRock\Slack\Logic\RequestSender\RequestSenderInterface')
            ->setMethods(array('send', '__construct'))
            ->getMock();

        $slackMessage = new Message($requestSenderStub);
        $this->assertInstanceOf('ClawRock\Slack\Logic\Message', $slackMessage->sendText('Test message'));
    }

    public function test_setting_message_data()
    {
        $messageData = new MessageData();
        $messageData->setArgument('text', 'test');
        $messageService = new StreamRequestSender(getenv('EMPTY_IP'));
        $message        = new Message($messageService);
        $message->setMessageData($messageData);
        $this->assertEquals($messageData, $message->getMessageData());
    }
}
