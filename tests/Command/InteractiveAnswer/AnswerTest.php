<?php

namespace ClawRock\Slack\Test\Command;

use ClawRock\Slack\Logic\Command\InteractiveAnswer\Answer;
use ClawRock\Slack\Logic\Request\InteractiveRequest;

class AnswerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var InteractiveRequest
     */
    protected $interactiveRequest;

    /**
     * @var InteractiveRequest
     */
    protected $interactiveRequest2;

    public function setUp()
    {
        $this->interactiveRequest  = new InteractiveRequest([
            'payload' => '{"callback_id":"Test1","actions":[{"name":"Action name","value":"Action value"}]}',
        ]);
        $this->interactiveRequest2 = new InteractiveRequest([
            'payload' => '{"callback_id":"Test1","actions":[{"name":"Another Action"}]}',
        ]);
    }

    public function test_selecting_proper_action_name()
    {
        $answer = new Answer('Action name');
        $this->assertTrue($answer->matchesActions($this->interactiveRequest));
        $this->assertFalse($answer->matchesActions($this->interactiveRequest2));
    }
}
