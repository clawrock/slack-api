<?php

namespace ClawRock\Slack\Logic\Command;

use ClawRock\Slack\Fluent\Response\MessageDataBuilderInterface;
use ClawRock\Slack\Logic\Request\InteractiveRequest;

class Answer
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var callable
     */
    protected $callable = null;

    /**
     * Answer constructor.
     * @param $name
     * @param $value
     */
    public function __construct($name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
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
     * @param InteractiveRequest $request
     * @return bool
     */
    protected function matchesActions(InteractiveRequest $request)
    {
        $actions = $request->getActions()[0];
        return ($actions['name'] == $this->name && $actions['value'] == $this->value);
    }
}
