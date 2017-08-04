<?php

namespace ClawRock\Slack\Test;

use ClawRock\Slack\SlackFactory;

class SlackFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_returned_types()
    {
        $this->assertInstanceOf('ClawRock\Slack\Logic\Message',
            SlackFactory::getMessageService(getenv('EMPTY_IP')));
        $this->assertInstanceOf('ClawRock\Slack\Logic\RequestSender\RequestSenderInterface',
            SlackFactory::getRequestSender(getenv('EMPTY_IP')));
        $this->assertInstanceOf('ClawRock\Slack\Fluent\Guard\GuardDecorator', SlackFactory::guard());
        $this->assertInstanceOf('ClawRock\Slack\Logic\Command\SlashCommand', SlackFactory::slashCommand('token'));
        $this->assertInstanceOf('ClawRock\Slack\Logic\Request\RequestInterface', SlackFactory::getRequest());
        $this->assertInstanceOf('ClawRock\Slack\Logic\Dispatcher', SlackFactory::dispatcher());
        $this->assertInstanceOf('ClawRock\Slack\Fluent\Response\MessageDataBuilderInterface',
            SlackFactory::getMessageDataBuilder());
        $this->assertInstanceOf('ClawRock\Slack\Fluent\Response\AttachmentBuilder',
            SlackFactory::getAttachmentBuilder());
    }

    public function test_logging()
    {
        $request = SlackFactory::getRequest(['payload' => 'asdddddda']);
    }
}
