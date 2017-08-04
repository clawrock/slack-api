<?php

namespace ClawRock\Slack\Test\Command;

use ClawRock\Slack\Common\CommandParameters;
use ClawRock\Slack\Common\Enum\Error;
use ClawRock\Slack\Fluent\Guard\GuardDecorator;
use ClawRock\Slack\Fluent\Response\ResponseBuilder;
use ClawRock\Slack\Fluent\Response\MessageDataBuilderInterface;
use ClawRock\Slack\Logic\Command\Command;
use ClawRock\Slack\Logic\Request\RequestInterface;
use ClawRock\Slack\Logic\Request\SlashRequest;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    private $requestTest1U1;
    private $requestTest2U1;
    private $response;

    public function setUp()
    {
        $this->requestTest1U1 = new SlashRequest(['text' => 'Test1', 'user_id' => 'U1']);
        $this->requestTest2U1 = new SlashRequest(['text' => 'Test2', 'user_id' => 'U1']);
        $this->response       = new ResponseBuilder();
    }

    public function test_invoke_basic_closure()
    {
        $command       = new Command();
        $closureResult = $command->run(function () {
            return 2 + 2;
        })->__invoke($this->requestTest1U1, $this->response);

        $this->assertContains(4, $closureResult);
    }

    public function test_invoke_slack_command()
    {
        $command      = new Command();
        $innerCommand = new Command();
        $command->run($innerCommand->run(function () {
            return 4 * 4;
        }));
        $commandResult = $command->__invoke($this->requestTest1U1, $this->response);

        $this->assertContains(16, $commandResult[0]);
    }

    public function test_invoke_regex_command()
    {
        $command = new Command();
        $command->on('/^Test1/')
            ->run(function () {
                return 'first regex';
            })
            ->on('/^Test2/')// Only this one should launch
            ->run(function () {
                return 'second regex';
            })
            ->on('/^Test3/')
            ->run(function () {
                return 'third regex';
            });
        $regexResult = $command->__invoke($this->requestTest2U1, $this->response);

        $this->assertFalse(in_array('first regex', $regexResult));
        $this->assertFalse(in_array('third regex', $regexResult));
        $this->assertTrue(in_array('second regex', $regexResult));
    }

    public function test_guard_inner_callable()
    {
        $command = new Command();
        $guard   = new GuardDecorator($this->requestTest1U1);
        $command->on('/^Test1/')
            ->addGuard($guard->denyUserIds(['U1']))
            ->run(function () {
                return 'first closure';
            })
            ->on('/^Test2/')
            ->run(function () {
                return 'second closure';
            });
        $firstCommandData = $command->__invoke($this->requestTest1U1, $this->response);

        $command = new Command();
        $guard   = new GuardDecorator($this->requestTest2U1);
        $command->on('/^Test1/')
            ->addGuard($guard->denyUserIds(['U1']))
            ->run(function () {
                return 'first closure';
            })
            ->on('/^Test2/')
            ->run(function () {
                return 'second closure';
            });
        $secondCommandData = $command->__invoke($this->requestTest2U1, $this->response);

        $this->assertEquals(Error::NOT_ALLOWED, $firstCommandData['Error']);
        $this->assertTrue(in_array('second closure', $secondCommandData));
    }

    public function test_error_if_no_valid_parameters()
    {
        $command = new Command();
        $commandData = $command->__invoke($this->requestTest1U1, $this->response);

        $this->assertEquals(Error::NO_PARAMETER_MATCH, $commandData['Error']);
    }

    public function test_passing_string_to_on_method_will_wrap_into_regex()
    {
        $command = new Command();
        $command->on('Test1')->run(function () {
            return 'Test passing';
        });
        $commandData = $command->__invoke($this->requestTest1U1, $this->response);

        $this->assertContains('Test passing', $commandData);
    }

    public function test_switching_through_container_scope()
    {
        $command       = new Command();
        $testContainer = $command->on('Test1')->run(function () {
            return 'First test';
        });
        $testContainer->run(function () {
            return 'Second test';
        });

        $command->run(function () {
            return 'Default function';
        });

        $firstResult  = $command->__invoke($this->requestTest1U1, $this->response);
        $secondResult = $command->__invoke($this->requestTest2U1, $this->response);

        $this->assertContains('First test', $firstResult);
        $this->assertContains('Second test', $firstResult);
        $this->assertNotContains('Default function', $firstResult);

        $this->assertNotContains('First test', $secondResult);
        $this->assertContains('Default function', $secondResult);

    }

    public function test_using_leftover_text_to_match_regex()
    {
        $request  = new SlashRequest(['text' => 'simple:command:test']);
        $command  = new Command();
        $command2 = new Command();
        $command3 = new Command();
        $command->on('simple')
            ->run(function () {
                return 'simple';
            })->run($command2
                ->on(':command')
                ->run(function () {
                    return 'command';
                })->run($command3
                    ->on(':not-valid')
                    ->run(function () {
                        return 'not-valid';
                    })->on(":test")
                    ->run(function () {
                        return 'test';
                    })));
        $result = $command->__invoke($request, $this->response);
        $this->assertContains('simple', $result);
        $this->assertContains('command', $result[1]);
        $this->assertContains('test', $result[1][1]);
        $this->assertNotContains('not-valid', $result[1][1]);
    }

    public function test_accessing_regex_match_and_leftover()
    {
        $request  = new SlashRequest(['text' => 'simple command leftover val']);
        $command  = new Command();
        $command2 = new Command();
        $command
            ->on('simple')
            ->run($command2
                ->on('command')
                ->run(function (
                    RequestInterface $req,
                    MessageDataBuilderInterface $res,
                    CommandParameters $params
                ) {
                    $res->addText('Match: \'' . $params->getMatchingValue() . '\' ');
                    $res->addText('Leftover: \'' . $params->getTextLeftover() . '\' ');
                }));
        $command->__invoke($request, $this->response);
        $messageData = $this->response->create();
        $this->assertEquals('Match: \'command\' Leftover: \'leftover val\' ', $messageData->getContent()['text']);
    }

    public function test_function_that_always_run()
    {
        $request  = new SlashRequest(['text' => 'simple command leftover val']);
        $command  = new Command();
        $command2 = new Command();
        $command
            ->runAlways(function (RequestInterface $req, MessageDataBuilderInterface $res) {
                $res->addText('This should always run');
            })
            ->on('simple')
            ->run($command2
                ->on('command')
                ->run(function (
                    RequestInterface $req,
                    MessageDataBuilderInterface $res
                ) {
                    $res->addText(' and this should run if regex match');
                })
                ->on('invalid command')
                ->run(function (
                    RequestInterface $req,
                    MessageDataBuilderInterface $res
                ) {
                    $res->addText(' but this should not be visible');
                })
            );
        $command->__invoke($request, $this->response);
        $messageData = $this->response->create();
        $this->assertEquals('This should always run and this should run if regex match',
            $messageData->getContent()['text']);
    }
}
