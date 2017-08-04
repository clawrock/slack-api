<?php

namespace ClawRock\Slack\Logic\Command;

use ClawRock\Slack\Common\Enum\RequestType;
use ClawRock\Slack\Fluent\Response\MessageDataBuilderInterface;
use ClawRock\Slack\Logic\Request\RequestInterface;

class InteractiveCommand extends AbstractCommand implements DispatcherCommandInterface
{

    /**
     * @var RequestType
     */
    protected $allowedRequestType;

    /**
     * InteractiveCommand constructor.
     * @param string $token
     */
    public function __construct($token)
    {
        if (!is_string($token)) {
            throw new \InvalidArgumentException(
                'Parameter must be a string, ' . gettype($token) . ' provided'
            );
        }
        $this->token                     = $token;
        $this->container[self::NO_MATCH] = new Container($this, self::NO_MATCH);
        $this->allowedRequestType        = RequestType::INTERACTIVE_COMMAND();
    }

    /**
     * @return RequestType
     */
    public function getAllowedRequestType()
    {
        return $this->allowedRequestType;
    }

    /**
     * @param RequestInterface            $request
     * @param MessageDataBuilderInterface $response
     * @return array
     */
    public function __invoke(RequestInterface $request, MessageDataBuilderInterface $response)
    {
        $matchingCallback = $this->getMatchingCallbackId($request->getCommandParameterString());
        $container        = $this->container[$matchingCallback];
        return $container->runCallables($request, $response);
    }

    /**
     * @param $value
     * @return string
     */
    protected function getMatchingCallbackId($value)
    {
        foreach (array_keys($this->container) as $callbackId) {
            if ($callbackId === self::NO_MATCH) {
                continue;
            }

            if ($value === $callbackId) {
                return $value;
            }
        }
        return self::NO_MATCH;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Returns container responsible for provided regex.
     * Creates new container if regex was not found.
     *
     * @param $callbackId
     * @return Container
     */
    public function on($callbackId)
    {
        if (empty($this->container[$callbackId])) {
            $container                    = new Container($this, $callbackId);
            $this->container[$callbackId] = $container;
        }
        return $this->container[$callbackId];
    }
}
