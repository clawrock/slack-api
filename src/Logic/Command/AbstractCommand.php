<?php

namespace ClawRock\Slack\Logic\Command;

use ClawRock\Slack\Fluent\Guard\GuardDecoratorInterface;

abstract class AbstractCommand implements CommandInterface
{
    /**
     * This constant represents default behavior when regex was not able to match any string.
     */
    const NO_MATCH = '';

    /**
     * @var Container[]
     */
    protected $container;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var callable
     */
    protected $runAlways = null;

    /**
     * @param  GuardDecoratorInterface $guard
     * @return $this
     */
    public function addGuard(GuardDecoratorInterface $guard)
    {
        $this->container[self::NO_MATCH]->addGuard($guard);
        return $this;
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function runAlways(callable $callable)
    {
        $this->runAlways = $callable;

        //In order to launch runAlways function we need to add dummy function, without it the dispatcher will
        //just throw an error.
        $this->run(function () {
        });
        return $this;
    }

    /**
     * @param  callable $callable
     * @return $this
     */
    public function run(callable $callable)
    {
        $this->container[self::NO_MATCH]->run($callable);
        return $this;
    }
}
