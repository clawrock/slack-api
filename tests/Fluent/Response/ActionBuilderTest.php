<?php

namespace ClawRock\Slack\Test\Fluent\Response;

use ClawRock\Slack\Common\Enum\ActionStyle;
use ClawRock\Slack\Fluent\Response\ActionBuilder;

class ActionBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test_setting_fields()
    {
        $actionBuilder = new ActionBuilder();
        $action        = $actionBuilder->setValue('value')
            ->setText('text')
            ->setName('name')
            ->setStyle(ActionStyle::DANGER())
            ->setConfirm('title', 'text', 'oktext', 'dismisstext')
            ->create();
        $actionData    = $action->getData();
        $this->assertArraySubset([
            'name'    => 'name',
            'type'    => 'button',
            'value'   => 'value',
            'text'    => 'text',
            'style'   => 'danger',
            'confirm' => [
                'title'        => 'title',
                'text'         => 'text',
                'ok_text'      => 'oktext',
                'dismiss_text' => 'dismisstext'
            ]
        ], $actionData);
    }

    public function test_reset_data()
    {
        $actionBuilder = new ActionBuilder();
        $action        = $actionBuilder->setText('non default text')
            ->reset()
            ->create();
        $this->assertContains('Action label', $action->getData());
        $this->assertNotContains('non default text', $action->getData());
    }
}
