<?php

namespace ClawRock\Slack\Logic\Command;

use ClawRock\Slack\Common\CommandParameters;
use ClawRock\Slack\Common\Enum\Error;
use ClawRock\Slack\Fluent\Guard\GuardDecorator;
use ClawRock\Slack\Fluent\Guard\GuardDecoratorInterface;
use ClawRock\Slack\Fluent\Response\MessageDataBuilderInterface;
use ClawRock\Slack\Logic\Request\RequestInterface;

class Container
{
    /**
     * @var CommandInterface
     */
    protected $parent;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var callable[]
     */
    protected $callables = [];

    /**
     * @var GuardDecorator[]
     */
    protected $guards = [];

    /**
     * @var MessageDataBuilderInterface
     */
    protected $messageDataBuilder;

    /**
     * SlackContainer constructor.
     * @param CommandInterface $parent
     * @param string           $name
     */
    public function __construct(CommandInterface $parent, $name)
    {
        $this->parent = $parent;
        $this->name   = $name;
    }

    /**
     * @return CommandInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Implements Callable Interface
     *
     * @param RequestInterface            $request
     * @param MessageDataBuilderInterface $response
     * @param CommandParameters           $params
     * @return array
     */
    public function __invoke(
        RequestInterface $request,
        MessageDataBuilderInterface $response,
        CommandParameters $params = null
    ) {
        return call_user_func($this->parent, $request, $response, $params);
    }

    /**
     * Iterates and run callables defined for Container.
     *
     * @param RequestInterface            $request
     * @param MessageDataBuilderInterface $response
     * @param CommandParameters           $params
     * @return array
     * @internal param string $textLeft
     */
    public function runCallables(
        RequestInterface $request,
        MessageDataBuilderInterface $response,
        CommandParameters $params = null
    ) {
        if ($this->isAllowed($request)) {
            $data = [];
            if (empty($this->callables)) {
                return ['Error' => Error::NO_PARAMETER_MATCH];
            }
            foreach ($this->callables as $callable) {
                $returnedValue = call_user_func($callable, $request, $response, $params);
                if (!is_null($returnedValue)) {
                    $data[] = $returnedValue;
                }
            }
            return $data;
        }
        return ['Error' => Error::NOT_ALLOWED];
    }

    /**
     * @param RequestInterface $request
     * @return bool
     */
    private function isAllowed(RequestInterface $request)
    {
        $isAllowed = true;
        foreach ($this->guards as $guard) {
            $isAllowed = min($isAllowed, $guard->isAllowed($request));
        }
        return $isAllowed;
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function run(callable $callable)
    {
        $this->callables[] = $callable;
        return $this;
    }

    /**
     * @param $value
     * @return string
     */
    public function on($value)
    {
        return $this->parent->on($value);
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
}
