<?php

namespace ClawRock\Slack\Logic\Command\InteractiveAnswer;

use ClawRock\Slack\Logic\Request\InteractiveRequest;

class MenuAnswer extends Answer
{
    /**
     * @var string
     */
    protected $selectedOption;

    /**
     * MenuAnswer constructor.
     * @param        $name
     * @param string $selectedOption
     */
    public function __construct($name, $selectedOption = '')
    {
        $this->selectedOption = $selectedOption;
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    public function matchesActions(InteractiveRequest $request)
    {
        if (parent::matchesActions($request)) {
            if ($this->selectedOption === '') {
                return $request->getSelectedOptionValue() !== null;
            }
            return $request->getSelectedOptionValue() === $this->selectedOption;
        }
        return false;
    }
}
