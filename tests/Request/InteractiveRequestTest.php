<?php

namespace ClawRock\Slack\Test\Request;

use ClawRock\Slack\Logic\Request\InteractiveRequest;

class InteractiveRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException ClawRock\Slack\Common\Exception\InvalidJsonException
     */
    public function test_throw_exception_if_payload_is_not_json()
    {
        $payload = ['payload' => 'non json'];
        $request = new InteractiveRequest($payload);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_throw_exception_if_invalid_request()
    {
        $request = ['name' => 'name', 'callback_id' => 'callback'];
        $request = new InteractiveRequest($request);
    }
}
