<?php

namespace ClawRock\Slack\Logic;

use ClawRock\Slack\Common\Enum\Group;
use ClawRock\Slack\Common\Enum\Permission;

class Guard
{
    const ALLOW = 'allow';
    const DENY = 'deny';

    /**
     * @var Permission
     */
    protected $defaultBehavior;

    /**
     * @var array
     */
    protected $allowRules = [];

    /**
     * @var array
     */
    protected $denyRules = [];

    /**
     * SlackGuard constructor.
     */
    public function __construct()
    {
        $this->defaultBehavior = Permission::DENY_ALL;
    }

    /**
     * @return Permission
     */
    public function getDefaultBehavior()
    {
        return $this->defaultBehavior;
    }

    /**
     * Sets default behaviour for incoming request.
     *
     * @param Permission $defaultBehavior
     *
     * @return Guard Returns self instance
     */
    public function setDefaultBehavior(Permission $defaultBehavior)
    {
        $this->defaultBehavior = $defaultBehavior;

        return $this;
    }

    /**
     * Adds allow permission for provided groups with Id.
     *
     * @param Group  $group
     * @param        $ids
     * @return $this
     */
    public function allow(Group $group, $ids)
    {
        $this->changePermission($group->getValue(), self::ALLOW, $ids);

        return $this;
    }

    /**
     * Sets permissions for given attributes.
     *
     * @param string       $groupName Name of the group you wish to change permissions for.
     * @param string       $permission Permission type. 'allow' or 'deny'
     * @param array|string $ids Ids for which you want to change permissions
     */
    protected function changePermission($groupName, $permission, $ids)
    {
        if (!is_string($ids) && !is_array($ids)) {
            throw new \InvalidArgumentException(
                'Only string and array parameters are valid. ' . gettype($ids) . ' provided'
            );
        }

        if (is_array($ids)) {
            foreach ($ids as $id) {
                $this->changePermission($groupName, $permission, $id);
            }

            return;
        }

        $this->createGroupArrayIfNotExists($groupName);

        if ($permission == self::ALLOW) {
            $this->denyRules[$groupName] = array_diff($this->denyRules[$groupName], [$ids]);

            if (!in_array($ids, $this->allowRules[$groupName])) {
                $this->allowRules[$groupName][] = $ids;
            }

            return;
        }

        $this->allowRules[$groupName] = array_diff($this->allowRules[$groupName], [$ids]);

        if (!in_array($ids, $this->denyRules[$groupName])) {
            $this->denyRules[$groupName][] = $ids;
        }
    }

    /**
     * Creates deny/allow arrays for new groups
     *
     * @param $groupName
     */
    protected function createGroupArrayIfNotExists($groupName)
    {
        if (empty($this->allowRules[$groupName])) {
            $this->allowRules[$groupName] = [];
        }
        if (empty($this->denyRules[$groupName])) {
            $this->denyRules[$groupName] = [];
        }
    }

    /**
     * Adds deny permission for provided groups with Id.
     *
     * @param Group  $group
     * @param        $ids
     * @return $this
     */
    public function deny(Group $group, $ids)
    {
        $this->changePermission($group->getValue(), self::DENY, $ids);

        return $this;
    }

    /**
     * Returns array with permissions
     *
     * @return array
     */
    public function getPermissions()
    {
        $permissions = [
            'defaultBehavior' => $this->defaultBehavior,
            'allowRules'      => $this->allowRules,
            'denyRules'       => $this->denyRules
        ];

        return $permissions;
    }
}
