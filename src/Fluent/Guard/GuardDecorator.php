<?php

namespace ClawRock\Slack\Fluent\Guard;

use ClawRock\Slack\Common\Enum\Group;
use ClawRock\Slack\Common\Enum\Permission;
use ClawRock\Slack\Logic\Guard;
use ClawRock\Slack\Logic\Request\RequestInterface;

class GuardDecorator implements GuardDecoratorInterface
{
    const ALLOWED = 1;
    const DENIED = 0;
    const NO_MATCH = -1;

    /**
     * @var array
     */
    protected $permissionPriority = [
        Group::USER,
        Group::CHANNEL,
        Group::TEAM
    ];

    /**
     * @var Guard
     */
    protected $guardInstance;

    public function __construct()
    {
        $this->guardInstance = new Guard();
    }

    /**
     * Sets default behavior when no match for allowed/denied group.
     *
     * @param Permission $permission
     * @return GuardDecorator $this Returns self instance.
     */
    public function defaultBehavior(Permission $permission)
    {
        $this->guardInstance->setDefaultBehavior($permission);
        return $this;
    }

    /**
     * Adds user ids to the allowed group.
     *
     * @param array $ids
     * @return GuardDecorator $this Returns self instance.
     */
    public function allowUserIds($ids)
    {
        $this->guardInstance->allow(Group::USER(), $ids);
        return $this;
    }

    /**
     * Adds user ids to the denied group.
     *
     * @param array $ids
     * @return GuardDecorator $this Returns self instance.
     */
    public function denyUserIds($ids)
    {
        $this->guardInstance->deny(Group::USER(), $ids);
        return $this;
    }

    /**
     * Adds team ids to the allowed group.
     *
     * @param array $ids
     * @return GuardDecorator $this Returns self instance.
     */
    public function allowTeamIds($ids)
    {
        $this->guardInstance->allow(Group::TEAM(), $ids);
        return $this;
    }

    /**
     * Adds team ids to the denied group.
     *
     * @param array $ids
     * @return GuardDecorator $this Returns self instance.
     */
    public function denyTeamIds($ids)
    {
        $this->guardInstance->deny(Group::TEAM(), $ids);
        return $this;
    }

    /**
     * Adds channel ids to the allowed group.
     *
     * @param array $ids
     * @return GuardDecorator $this Returns self instance.
     */
    public function allowChannelIds($ids)
    {
        $this->guardInstance->allow(Group::CHANNEL(), $ids);
        return $this;
    }

    /**
     * Adds channel ids to the denied group.
     *
     * @param array $ids
     * @return GuardDecorator $this Returns self instance.
     */
    public function denyChannelIds($ids)
    {
        $this->guardInstance->deny(Group::CHANNEL(), $ids);
        return $this;
    }

    /**
     * @param RequestInterface $request
     * @return bool
     */
    public function isAllowed(RequestInterface $request)
    {
        return (bool)$this->checkPermissions($request);
    }

    /**
     * @param RequestInterface $request
     * @return int
     */
    protected function checkPermissions(RequestInterface $request)
    {
        $permissions = $this->guardInstance->getPermissions();
        foreach ($this->permissionPriority as $singleGroup) {
            if (array_key_exists($singleGroup, $permissions['allowRules'])) {
                $groupIdFunction = 'get' . $singleGroup . 'Id';
                if (in_array($request->$groupIdFunction(), $permissions['allowRules'][$singleGroup])) {
                    return self::ALLOWED;
                }
                if (in_array($request->$groupIdFunction(), $permissions['denyRules'][$singleGroup])) {
                    return self::DENIED;
                }
            }
        }

        if ($this->guardInstance->getDefaultBehavior() == Permission::DENY_ALL()) {
            return self::DENIED;
        }
        return self::ALLOWED;
    }
}
