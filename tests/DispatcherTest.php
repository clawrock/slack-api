<?php

namespace ClawRock\Slack\Test;

use ClawRock\Slack\Common\Enum\Error;
use ClawRock\Slack\Fluent\Guard\GuardDecorator;
use ClawRock\Slack\Logic\Command\InteractiveCommand;
use ClawRock\Slack\Logic\Command\SlashCommand;
use ClawRock\Slack\Logic\Dispatcher;
use ClawRock\Slack\Logic\Request\InteractiveRequest;
use ClawRock\Slack\Logic\Request\SlashRequest;
use ClawRock\Slack\SlackFactory;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{

    private $slashRequest;
    private $interactiveRequest;

    public function setUp()
    {
        $this->slashRequest       = new SlashRequest([
            'token'   => 'TOKEN1',
            'user_id' => 'U1'
        ]);
        $json                     =
            ['payload' => '{"actions":[{"name":"war","value":"war"}],"callback_id":"simple_callback","team":{"id":"a1x4556","domain":"clawrock"},"channel":{"id":"G3K8U4QUQ","name":"privategroup"},"user":{"id":"U3KMAQQTT","name":"office"},"action_ts":"1485333856.717065","message_ts":"1485333853.000003","attachment_id":"1","token":"TOKEN1","response_url":"https:\/\/hooks.slack.com\/actions\/T0DTKPES3\/131465735312\/PxHkAV9fIQn7iaEIqisWa5Co"}'];
        $this->interactiveRequest = new InteractiveRequest($json);
    }

    public function test_basic_dispatcher_functions()
    {
        $dispatcher      = new Dispatcher();
        $dispatcher      = $dispatcher->addCommand(SlackFactory::slashCommand('TOKEN1')
            ->run(function ($req, $res) {
                $res->setText('basic example');
            }));
        $responseBuilder = $dispatcher->dispatch($this->slashRequest);

        $this->assertEquals('basic example', $responseBuilder->create()->getContent()['text']);
    }

    public function test_invalid_parameter()
    {
        $request         = new SlashRequest([
            'token'   => 'TOKEN1',
            'user_id' => 'U1',
            'command' => '/do',
            'text'    => 'something'
        ]);
        $dispatcher      = new Dispatcher();
        $dispatcher      = $dispatcher->addCommand(SlackFactory::slashCommand('TOKEN1', '/do')
            ->on('something-else')
            ->run(function ($req, $res) {
                $res->setText('basic example');
            })->getParent());
        $responseBuilder = $dispatcher->dispatch($request);

        $this->assertEquals('I don\'t know this parameter', $responseBuilder->create()->getContent()['text']);
    }

    public function test_dispatcher_guard()
    {
        $dispatcher      = new Dispatcher();
        $guard           = new GuardDecorator();
        $responseBuilder = $dispatcher->addGuard($guard->denyUserIds('U1'))
            ->addCommand(SlackFactory::slashCommand('TOKEN1')
                ->run(function ($req, $res) {
                    $res->addText('basic example');
                }))
            ->dispatch($this->slashRequest);

        $this->assertEquals(Error::NOT_ALLOWED, $responseBuilder->create()->getContent()['text']);
    }

    public function test_dispatcher_denied_user()
    {
        $dispatcher      = new Dispatcher();
        $guard           = new GuardDecorator($this->interactiveRequest);
        $responseBuilder = $dispatcher->addGuard($guard->denyUserIds('U1'))
            ->addCommand(SlackFactory::slashCommand('TOKEN1')
                ->run(function () {
                    return 'basic example';
                }))
            ->dispatch($this->slashRequest);

        $this->assertEquals(Error::NOT_ALLOWED, $responseBuilder->create()->getContent()['text']);
    }

    public function test_dispatcher_many_commands()
    {
        $dispatcher = new Dispatcher();

        $responseBuilder = $dispatcher
            ->addCommand(SlackFactory::slashCommand('TOKEN3')
                ->run(function ($req, $res) {
                    $res->addText('TOKEN3 command');
                }))
            ->addCommand(SlackFactory::slashCommand('TOKEN2')
                ->run(function ($req, $res) {
                    $res->addText('TOKEN2 command');
                }))
            ->addCommand(SlackFactory::slashCommand('TOKEN1')
                ->run(function ($req, $res) {
                    $res->addText('TOKEN1 command');
                }))
            ->dispatch($this->slashRequest);

        $this->assertEquals('TOKEN1 command', $responseBuilder->create()->getContent()['text']);
    }

    public function test_interactive_request_dispatch()
    {
        $dispatcher         = new Dispatcher();
        $slashCommand       = new SlashCommand('TOKEN1');
        $interactiveCommand = new InteractiveCommand('TOKEN1');

        $dispatcherObject = $dispatcher->addCommand(
            $slashCommand->run(function ($req, $res) {
                $res->addText("slash function");
            }))
            ->addCommand($interactiveCommand->run(function ($req, $res) {
                $res->addText("interactive function");
            }));

        $slashDataBuilder = $dispatcherObject->dispatch($this->slashRequest);
        $slashResponse    = $slashDataBuilder->create()->getContent()['text'];

        $interactiveDataBuilder = $dispatcherObject->dispatch($this->interactiveRequest);
        $interactiveResponse    = $interactiveDataBuilder->create()->getContent()['text'];

        $this->assertEquals('slash function', $slashResponse);
        $this->assertEquals('interactive function', $interactiveResponse);
    }

    public function test_slashcommand_command_string_token()
    {
        $dispatcher  = new Dispatcher();
        $helpCommand = new SlashCommand('some-token', '/help');
        $doCommand   = new SlashCommand('some-token', '/do');
        $request     = new SlashRequest([
            'command' => '/do',
            'token'   => 'some-token',
            'user_id' => 'U1'
        ]);

        $dispatcherObject = $dispatcher->addCommand(
            $helpCommand->run(function ($req, $res) {
                $res->addText("help function");
            }))
            ->addCommand($doCommand->run(function ($req, $res) {
                $res->addText("do function");
            }));

        $slashDataBuilder = $dispatcherObject->dispatch($request);

        $this->assertEquals('do function', $slashDataBuilder->getData()['text']);
    }

    public function test_slashcommand_run_default_command()
    {
        $dispatcher     = new Dispatcher();
        $helpCommand    = new SlashCommand('some-token', '/help');
        $doCommand      = new SlashCommand('some-token', '/do');
        $defaultCommand = new SlashCommand('some-token');
        $request        = new SlashRequest([
            'command' => '/non-of-these-above',
            'token'   => 'some-token',
            'user_id' => 'U1'
        ]);

        $dispatcherObject = $dispatcher->addCommand(
            $helpCommand->run(function ($req, $res) {
                $res->addText("help function");
            }))
            ->addCommand($defaultCommand->run(function ($req, $res) {
                $res->addText("default function");
            }))
            ->addCommand($doCommand->run(function ($req, $res) {
                $res->addText("do function");
            }));

        $slashDataBuilder = $dispatcherObject->dispatch($request);

        $this->assertEquals('default function', $slashDataBuilder->getData()['text']);
    }

}
