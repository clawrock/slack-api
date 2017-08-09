<?php

namespace ClawRock\Slack\Test\Command;

use ClawRock\Slack\Logic\Command\InteractiveAnswer\ButtonAnswer;
use ClawRock\Slack\Logic\Request\InteractiveRequest;

class ButtonAnswerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var InteractiveRequest
     */
    protected $interactiveRequest;

    /**
     * @var InteractiveRequest
     */
    protected $interactiveRequest2;
    /**
     * @var InteractiveRequest
     */
    protected $interactiveRequest3;

    /**
     * @var InteractiveRequest
     */
    protected $interactiveRequestWithoutActions;

    public function setUp()
    {
        $this->interactiveRequest               = new InteractiveRequest([
            'payload' => '{"callback_id":"Test1","actions":[{"name":"Action name","value":"Action value"}]}',
        ]);
        $this->interactiveRequest2              = new InteractiveRequest([
            'payload' => '{"callback_id":"Test1","actions":[{"name":"Another Action"}]}',
        ]);
        $this->interactiveRequest3              = new InteractiveRequest([
            'payload' => '{"callback_id":"Test1","actions":[{"name":"Action name", "value":"Action another value"}]}',
        ]);
        $this->interactiveRequestWithoutActions = new InteractiveRequest([
            'payload' => '{"callback_id":"Test1"}',
        ]);
    }

    public function test_selecting_proper_action_name()
    {
        $answer = new ButtonAnswer('Action name', 'Action value');
        $this->assertTrue($answer->matchesActions($this->interactiveRequest));
        $this->assertFalse($answer->matchesActions($this->interactiveRequest2));
        $this->assertFalse($answer->matchesActions($this->interactiveRequest3));
        $this->assertFalse($answer->matchesActions($this->interactiveRequestWithoutActions));
    }
}
