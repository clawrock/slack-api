<?php

namespace ClawRock\Slack\Logic\Response\Attachment;

class Action implements \JsonSerializable
{
    protected $data;

    /**
     * Action constructor.
     * @param        $name
     * @param        $text
     * @param string $type
     */
    public function __construct($name, $text, $type = 'button')
    {
        $this->throwExceptionIfTypeNotValid($name, 'string');
        $this->throwExceptionIfTypeNotValid($text, 'string');
        $this->throwExceptionIfTypeNotValid($type, 'string');

        $this->data['name'] = $name;
        $this->data['text'] = $text;
        $this->data['type'] = $type;
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
