<?php

namespace ClawRock\Slack\Common;

class CommandParameters
{
    protected $matchingValue;

    protected $textLeftover;

    public function __construct($matchingValue, $textLeftover)
    {
        $this->matchingValue = $matchingValue ? $matchingValue : '';
        $this->textLeftover  = $textLeftover ? trim($textLeftover) : '';
    }

    public function getMatchingValue()
    {
        return $this->matchingValue;
    }

    public function getTextLeftover()
    {
        return $this->textLeftover;
    }
}
