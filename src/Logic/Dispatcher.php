<?php

namespace ClawRock\Slack\Logic;

use ClawRock\Slack\Common\Enum\Error;
use ClawRock\Slack\Fluent\Guard\GuardDecoratorInterface;
use ClawRock\Slack\Fluent\Response\MessageDataBuilderInterface;
use ClawRock\Slack\Logic\Command\CommandInterface;
use ClawRock\Slack\Logic\Command\DispatcherCommandInterface;
use ClawRock\Slack\Logic\Command\SlashCommand;
use ClawRock\Slack\Logic\Request\RequestInterface;
use ClawRock\Slack\Logic\Request\SlashRequest;
use ClawRock\Slack\SlackFactory;

class Dispatcher
{
    /**
     * @var array
     */
    protected $command = [];

    /**
     * @var array
     */
    protected $guards = [];

    /**
     * Adds command to the array.
     *
     * IMPORTANT - commands without token will be omitted.
     *
     * @param DispatcherCommandInterface $command
     * @return $this
     */
    public function addCommand(DispatcherCommandInterface $command)
    {
        $requestType = $command->getAllowedRequestType();
        $token       = $command->getToken();

        $this->addNewCommand($command, $requestType->getValue(), $token);
        return $this;
    }

    protected function addNewCommand(DispatcherCommandInterface $command, $requestType, $token)
    {
        if ($command instanceof SlashCommand) {
            $this->command[$requestType][$token][$command->getCommand()] = $command;
            return;
        }
        $this->command[$requestType][$token] = $command;
    }

    /**
     * @param GuardDecoratorInterface $guard
     * @return $this
     */
    public function addGuard(GuardDecoratorInterface $guard)
    {
        $this->guards[] = $guard;
        return $this;
    }

    /**
     * Dispatches a request to every command that matches the token
     *
     * @param RequestInterface|null $request
     * @return MessageDataBuilderInterface
     */
    public function dispatch(RequestInterface $request = null)
    {
        if (is_null($request)) {
            $request = SlackFactory::getRequest();
        }

        $requestType = $request->getRequestType()->getValue();
        $response    = SlackFactory::getMessageDataBuilder();

        $command = $this->getCommand($request);

        if ($command == null) {
            return $response->addText(Error::COMMAND_NOT_FOUND);
        }

        $requestToken = $request->getToken();

        if ($requestToken == '' || !$this->isRequestPermitted($request)) {
            return $response->addText(Error::NOT_ALLOWED);
        }

        $this->handleOutput($command->__invoke($request, $response), $response);

        return $response;
    }

    /**
     * Returns command identifier - token or command (eg. '/task') or false if no match
     *
     * @param RequestInterface $request
     * @return CommandInterface|null
     */
    protected function getCommand(RequestInterface $request)
    {
        $requestType = $request->getRequestType();
        $token       = $request->getToken();
        if (empty($this->command[$requestType->getValue()][$token])) {
            return null;
        }
        $commands = $this->command[$requestType->getValue()][$token];
        if ($request instanceof SlashRequest) {
            if (!empty($commands[$request->getCommand()])) {
                return $commands[$request->getCommand()];
            }
            if (!empty($commands[''])) {
                return $commands[''];
            }
            return null;
        }
        return $commands;
    }

    /**
     * Checks every guard whether the request has permission to run the commands.
     *
     * @param RequestInterface $request
     * @return bool|mixed
     */
    protected function isRequestPermitted(RequestInterface $request)
    {
        $isAllowed = true;
        foreach ($this->guards as $guard) {
            $isAllowed = min($isAllowed, $guard->isAllowed($request));
        }
        return $isAllowed;
    }

    protected function handleOutput($data, MessageDataBuilderInterface $response)
    {
        if (is_array($data) && !empty($data['Error'])) {
            $response->setText($data['Error']);
        }
    }
}
