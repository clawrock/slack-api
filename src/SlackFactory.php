<?php

namespace ClawRock\Slack;

use ClawRock\Slack\Common\Exception\InvalidJsonException;
use ClawRock\Slack\Fluent\Guard\GuardDecorator;
use ClawRock\Slack\Fluent\Response\AttachmentBuilder;
use ClawRock\Slack\Fluent\Response\MessageDataBuilderInterface;
use ClawRock\Slack\Fluent\Response\ResponseBuilder;
use ClawRock\Slack\Fluent\Response\SnippetBuilder;
use ClawRock\Slack\Logic\Command\Command;
use ClawRock\Slack\Logic\Command\InteractiveAnswer\Answer;
use ClawRock\Slack\Logic\Command\InteractiveAnswer\ButtonAnswer;
use ClawRock\Slack\Logic\Command\InteractiveAnswer\MenuAnswer;
use ClawRock\Slack\Logic\Command\InteractiveCommand;
use ClawRock\Slack\Logic\Command\SlashCommand;
use ClawRock\Slack\Logic\Dispatcher;
use ClawRock\Slack\Logic\Message;
use ClawRock\Slack\Logic\Request\InteractiveRequest;
use ClawRock\Slack\Logic\Request\RequestInterface;
use ClawRock\Slack\Logic\Request\SlashRequest;
use ClawRock\Slack\Logic\RequestSender\StreamRequestSender;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SlackFactory
{
    /**
     * @var array
     */
    protected static $requestSource = null;

    /**
     * @var Logger
     */
    protected static $loggerInstance = null;

    /**
     * @var RequestInterface
     */
    protected static $request = null;

    public function __construct(array $config)
    {
        self::$requestSource  = $config['requestSource'];
        self::$loggerInstance = $config['loggerInstance'];
    }

    /**
     * Returns class that is responsible for sending messages to Slack's API.
     *
     * @param string $url Slack's API endpoint url
     *
     * @return Message() Returns new instance of Message() class
     */
    public static function getMessageService($url)
    {
        return new Message(self::getRequestSender($url));
    }

    /**
     * @param string $token
     * @return SnippetBuilder
     */
    public static function snippet($token)
    {
        return new SnippetBuilder($token);
    }

    /**
     * Returns implementation of RequestSender interface
     *
     * @param $url
     * @return StreamRequestSender
     */
    public static function getRequestSender($url)
    {
        return new StreamRequestSender($url);
    }

    /**
     * Returns instance of GuardDecorator
     *
     * @return GuardDecorator;
     */
    public static function guard()
    {
        return new GuardDecorator();
    }

    /**
     * Returns instance of SlackCommand
     *
     * @param string $token
     * @param string $command
     * @return SlashCommand
     */
    public static function slashCommand($token, $command = '')
    {
        return new SlashCommand($token, $command);
    }

    /**
     * @param array|null $requestData
     * @return InteractiveRequest|RequestInterface|SlashRequest
     */
    public static function getRequest($requestData = null)
    {
        if (empty($requestData)) {
            $requestData = self::getRequestSource();
        }
        if (empty(self::$request)) {
            self::$request = self::determineRequestType($requestData);
        }

        return self::$request;
    }

    protected static function getRequestSource()
    {
        return (is_null(self::$requestSource)) ? $_POST : self::$requestSource;
    }

    /**
     * @param $request
     * @return InteractiveRequest|SlashRequest
     */
    protected static function determineRequestType($request)
    {
        if (!empty($request['payload'])) {
            try {
                return new InteractiveRequest($request);
            } catch (InvalidJsonException $e) {
                self::getLoggerInstance()->addError('Request\'s payload fields was not valid json format.');
                self::getLoggerInstance()->addDebug('Invalid json: ' . $request['payload']);
            }
        }
        return new SlashRequest($request);
    }

    /**
     * @return Logger
     */
    public static function getLoggerInstance()
    {
        if (self::$loggerInstance == null) {
            self::$loggerInstance = new Logger('error-logger');
            self::$loggerInstance->pushHandler(new StreamHandler(dirname(dirname(__FILE__)) . '/logs/app.log'));
        }
        return self::$loggerInstance;
    }

    /**
     * Returns instance of SlackDispatcher
     *
     * @return Dispatcher()
     */
    public static function dispatcher()
    {
        return new Dispatcher();
    }

    /**
     * Returns instance of ResponseBuilder
     *
     * @return MessageDataBuilderInterface
     */
    public static function getMessageDataBuilder()
    {
        return new ResponseBuilder();
    }

    /**
     * Returns instance of ResponseBuilder
     *
     * @return AttachmentBuilder
     */
    public static function getAttachmentBuilder()
    {
        return new AttachmentBuilder();
    }

    /**
     * @param string $name
     * @param string $value
     * @return Answer
     */
    public static function answer($name)
    {
        return new Answer($name);
    }

    /**
     * @param        $name
     * @param string $value
     * @return ButtonAnswer
     */
    public static function buttonAnswer($name, $value = '')
    {
        return new ButtonAnswer($name, $value);
    }

    /**
     * @param        $name
     * @param string $value
     * @return MenuAnswer
     */
    public static function menuAnswer($name, $value = '')
    {
        return new MenuAnswer($name, $value);
    }

    /**
     * @param string $token
     * @return InteractiveCommand
     */
    public static function interactiveCommand($token)
    {
        return new InteractiveCommand($token);
    }

    public static function command()
    {
        return new Command();
    }
}
