<?php

namespace ClawRock\Slack\Test;

use ClawRock\Slack\Common\Enum\Group;
use ClawRock\Slack\Common\Enum\Permission;
use ClawRock\Slack\Logic\Guard;

class GuardTest extends \PHPUnit_Framework_TestCase
{
    public function test_set_default_behavior()
    {
        $guard = new Guard();
        $guard->setDefaultBehavior(Permission::ALLOW_ALL());
        $permissions = $guard->getPermissions();
        $this->assertArrayHasKey('defaultBehavior', $permissions);
        $this->assertEquals(Permission::ALLOW_ALL(), $guard->getDefaultBehavior());
    }

    public function test_add_allow_permission()
    {
        $guard = new Guard();
        $guard->allow(Group::USER(), ['U1']);
        $permissions = $guard->getPermissions();
        $this->assertContains('U1', $permissions['allowRules'][Group::USER]);
    }

    public function test_add_deny_permission()
    {
        $guard = new Guard();
        $guard->deny(Group::USER(), ['U1']);
        $permissions = $guard->getPermissions();
        $this->assertContains('U1', $permissions['denyRules'][Group::USER]);
    }

    public function test_allowed_then_denied_user_is_only_in_deny_rules_array()
    {
        $guard = new Guard();
        $guard->allow(Group::USER(), ['U1']);
        $guard->deny(Group::USER(), ['U1']);
        $permissions = $guard->getPermissions();
        $this->assertNotContains('U1', $permissions['allowRules'][Group::USER]);
        $this->assertContains('U1', $permissions['denyRules'][Group::USER]);
    }
}
