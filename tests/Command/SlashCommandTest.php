<?php

namespace ClawRock\Slack\Test\Command;

use ClawRock\Slack\Common\CommandParameters;
use ClawRock\Slack\Common\Enum\Error;
use ClawRock\Slack\Fluent\Guard\GuardDecorator;
use ClawRock\Slack\Fluent\Response\ResponseBuilder;
use ClawRock\Slack\Fluent\Response\MessageDataBuilderInterface;
use ClawRock\Slack\Logic\Command\SlashCommand;
use ClawRock\Slack\Logic\Request\RequestInterface;
use ClawRock\Slack\Logic\Request\SlashRequest;

class SlashCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_non_string_command_token_throws_exception()
    {
        $command = new SlashCommand(123);
    }

    public function test_accessors()
    {
        $command = new SlashCommand('token', '/command');
        $this->assertEquals('/command', $command->getCommand());
        $this->assertEquals('token', $command->getToken());
    }
}
