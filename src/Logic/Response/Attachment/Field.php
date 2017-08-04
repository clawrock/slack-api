<?php

namespace ClawRock\Slack\Logic\Response\Attachment;

class Field implements \JsonSerializable
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Field constructor.
     * @param string  $title
     * @param string  $value
     * @param boolean $isShort
     */
    public function __construct($title, $value, $isShort)
    {
        $this->throwExceptionIfTypeNotValid($title, 'string');
        $this->throwExceptionIfTypeNotValid($value, 'string');
        $this->throwExceptionIfTypeNotValid($isShort, 'bool');

        $this->data['title'] = $title;
        $this->data['value'] = $value;
        $this->data['short'] = $isShort;
    }

    /**
     * @param string $value
     * @param string $type
     */
    protected function throwExceptionIfTypeNotValid($value, $type)
    {
        $type = 'is_' . $type;
        if (!$type($value)) {
            throw new \InvalidArgumentException('Parameter must be a ' . $type . ', ' . gettype($value) . ' provided');
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->data;
    }
}
