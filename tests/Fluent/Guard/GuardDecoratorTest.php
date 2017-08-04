<?php

namespace ClawRock\Slack\Test\Fluent\Guard;

use ClawRock\Slack\Common\Enum\Permission;
use ClawRock\Slack\Fluent\Guard\GuardDecorator;
use ClawRock\Slack\Logic\Request\SlashRequest;

class SlackGuardDecoratorTest extends \PHPUnit_Framework_TestCase
{
    private $request;

    public function setUp()
    {
        $this->request = new SlashRequest(['user_id' => 'U1', 'team_id' => 'T1', 'channel_id' => 'C1']);
    }

    public function test_default_allow_all_behavior()
    {
        $guard = new GuardDecorator();
        $guard->defaultBehavior(Permission::ALLOW_ALL());
        $this->assertTrue($guard->isAllowed($this->request));
    }

    public function test_default_deny_all_behavior()
    {
        $guard = new GuardDecorator();
        $guard->defaultBehavior(Permission::DENY_ALL());
        $this->assertFalse($guard->isAllowed($this->request));
    }

    public function test_allowing_user()
    {
        $guard = new GuardDecorator($this->request);
        $guard->defaultBehavior(Permission::DENY_ALL());
        $guard->allowUserIds(['U1']);
        $this->assertTrue($guard->isAllowed($this->request));
    }

    public function test_denying_user()
    {
        $guard = new GuardDecorator($this->request);
        $guard->defaultBehavior(Permission::ALLOW_ALL());
        $guard->denyUserIds(['U1']);
        $this->assertFalse($guard->isAllowed($this->request));
    }

    public function test_allowing_team()
    {
        $guard = new GuardDecorator($this->request);
        $guard->defaultBehavior(Permission::DENY_ALL());
        $guard->allowTeamIds(['T1']);
        $this->assertTrue($guard->isAllowed($this->request));
    }

    public function test_denying_team()
    {
        $guard = new GuardDecorator($this->request);
        $guard->defaultBehavior(Permission::ALLOW_ALL());
        $guard->denyTeamIds(['T1']);
        $this->assertFalse($guard->isAllowed($this->request));
    }

    public function test_allowing_channel()
    {
        $guard = new GuardDecorator();
        $guard->defaultBehavior(Permission::DENY_ALL());
        $guard->allowChannelIds(['C1']);
        $this->assertTrue($guard->isAllowed($this->request));
    }

    public function test_denying_channel()
    {
        $guard = new GuardDecorator();
        $guard->defaultBehavior(Permission::ALLOW_ALL());
        $guard->denyChannelIds(['C1']);
        $this->assertFalse($guard->isAllowed($this->request));
    }

    public function test_allow_user_deny_team()
    {
        $guard = new GuardDecorator();
        $guard->allowUserIds(['U1']);
        $guard->denyTeamIds(['T1']);
        $this->assertTrue($guard->isAllowed($this->request));
    }

    public function test_deny_user_allow_team()
    {
        $guard = new GuardDecorator();
        $guard->allowTeamIds(['T1']);
        $guard->denyUserIds(['U1']);
        $this->assertFalse($guard->isAllowed($this->request));
    }
}
