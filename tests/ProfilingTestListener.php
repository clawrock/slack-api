<?php

namespace ClawRock\Slack\Test;

class ProfilingTestListener extends \PHPUnit_Framework_BaseTestListener
{
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        printf(
            "Test '%s' ended.\tTotal time %s s.\tTest time %s s.\n",
            str_pad($test->toString(), 50),
            number_format($test->getTestResultObject()->time(), 3),
            number_format($time, 3)
        );
    }
}
