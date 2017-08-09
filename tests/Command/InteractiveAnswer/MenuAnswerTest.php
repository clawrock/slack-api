<?php

namespace ClawRock\Slack\Test\Command;

use ClawRock\Slack\Logic\Command\InteractiveAnswer\MenuAnswer;
use ClawRock\Slack\Logic\Request\InteractiveRequest;

class MenuAnswerTest extends \PHPUnit_Framework_TestCase
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

    public function setUp()
    {
        $this->interactiveRequest  = new InteractiveRequest([
            'payload' => '{"callback_id":"Test1","actions":[{"name":"Action name","value":"Action value"}]}',
        ]);
        $this->interactiveRequest2 = new InteractiveRequest([
            'payload' => '{"callback_id":"Test1","actions":[{"name":"Another Action"}]}',
        ]);
        $this->interactiveRequest3 = new InteractiveRequest([
            'payload' => '{"callback_id":"Test1","actions":[{"name":"Action name","selected_options":[{"value": "Action another value"}]}]}',
        ]);
    }

    public function test_selecting_proper_action_name()
    {
        $answer = new MenuAnswer('Action name', 'Action another value');
        $this->assertFalse($answer->matchesActions($this->interactiveRequest));
        $this->assertFalse($answer->matchesActions($this->interactiveRequest2));
        $this->assertTrue($answer->matchesActions($this->interactiveRequest3));
    }
}
