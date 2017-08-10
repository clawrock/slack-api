<?php

namespace ClawRock\Slack\Logic\Response\Attachment;

use ClawRock\Slack\Common\Enum\ActionType;

class Action implements \JsonSerializable
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Action constructor.
     * @param ActionType $actionType
     * @param            $name
     * @param            $text
     */
    public function __construct(ActionType $actionType, $name, $text)
    {
        $this->throwExceptionIfTypeNotValid($name, 'string');
        $this->throwExceptionIfTypeNotValid($text, 'string');

        $this->data['name'] = $name;
        $this->data['text'] = $text;
        $this->data['type'] = $actionType->getValue();
    }

    /**
     * @param $value
     * @param $type
     */
    protected function throwExceptionIfTypeNotValid($value, $type)
    {
        $type = 'is_' . $type;
        if (!$type($value)) {
            throw new \InvalidArgumentException('Parameter must be a ' . $type . ', ' . gettype($value) . ' provided');
        }
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setField($name, $value)
    {
        $this->data[$name] = $value;
        return $this;
    }
}
