<?php

namespace ClawRock\Slack\Logic\Command\InteractiveAnswer;

use ClawRock\Slack\Fluent\Response\MessageDataBuilderInterface;
use ClawRock\Slack\Logic\Request\InteractiveRequest;

class Answer
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var callable
     */
    protected $callable = null;

    /**
     * Answer constructor.
     * @param $name
     * @param $value
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param InteractiveRequest $request
     * @return mixed|null
     */
    public function __invoke(InteractiveRequest $request, MessageDataBuilderInterface $response)
    {
        if ($this->matchesActions($request) && !is_null($this->callable)) {
            return call_user_func($this->callable, $request, $response);
        }
        return null;
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function setRun(callable $callable)
    {
        $this->callable = $callable;
        return $this;
    }

    /**
     * @param InteractiveRequest $request
     * @return bool
     */
    public function matchesActions(InteractiveRequest $request)
    {
        $actions = $request->getActions();
        return !empty($actions[0]) && $actions[0]['name'] === $this->name;
    }
}
