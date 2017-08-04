<?php

namespace ClawRock\Slack\Fluent\Guard;

use ClawRock\Slack\Common\Enum\Permission;
use ClawRock\Slack\Logic\Request\RequestInterface;

interface GuardDecoratorInterface
{
    /**
     * Sets default behavior when no groups allowed/denied.
     *
     * @param Permission $permission
     * @return GuardDecoratorInterface $this Returns self instance.
     */
    public function defaultBehavior(Permission $permission);

    /**
     * Adds user ids to the allowed group.
     *
     * @param array $ids
     * @return GuardDecoratorInterface $this Returns self instance.
     */
    public function allowUserIds($ids);

    /**
     * Adds user ids to the denied group.
     *
     * @param array $ids
     * @return GuardDecoratorInterface $this Returns self instance.
     */
    public function denyUserIds($ids);

    /**
     * Adds team ids to the allowed group.
     *
     * @param array $ids
     * @return GuardDecoratorInterface $this Returns self instance.
     */
    public function allowTeamIds($ids);

    /**
     * Adds team ids to the denied group.
     *
     * @param array $ids
     * @return GuardDecoratorInterface $this Returns self instance.
     */
    public function denyTeamIds($ids);

    /**
     * Adds channel ids to the allowed group.
     *
     * @param array $ids
     * @return GuardDecoratorInterface $this Returns self instance.
     */
    public function allowChannelIds($ids);

    /**
     * Adds channel ids to the denied group.
     *
     * @param array $ids
     * @return GuardDecoratorInterface $this Returns self instance.
     */
    public function denyChannelIds($ids);

    /**
     * True if the data from request is allowed to invoke commands.
     *
     * @param RequestInterface $request
     * @return bool
     */
    public function isAllowed(RequestInterface $request);
}
