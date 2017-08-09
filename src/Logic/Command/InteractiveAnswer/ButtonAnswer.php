<?php

namespace ClawRock\Slack\Logic\Command\InteractiveAnswer;

use ClawRock\Slack\Logic\Request\InteractiveRequest;

class ButtonAnswer extends Answer
{
    /**
     * @var string
     */
    protected $value;

    /**
     * ButtonAnswer constructor.
     * @param        $name
     * @param string $value
     */
    public function __construct($name, $value = '')
    {
        $this->value = $value;
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    public function matchesActions(InteractiveRequest $request)
    {
        if (parent::matchesActions($request)) {
            if ($this->value === '') {
                return $request->getValue() !== null;
            }
            return $request->getValue() === $this->value;
        }
        return false;
    }
}
