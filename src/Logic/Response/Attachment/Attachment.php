<?php

namespace ClawRock\Slack\Logic\Response\Attachment;

class Attachment implements \JsonSerializable
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Attachment constructor.
     * @param array|null $data
     */
    public function __construct($data = null)
    {
        if (!is_array($data) && !is_null($data)) {
            throw new \InvalidArgumentException('Parameter must be a string or null, ' . gettype($data) . ' provided');
        }
        $this->data = !is_null($data) ? $data : [];
    }

    /**
     * @param Field $field
     */
    public function addField(Field $field)
    {
        $this->data['fields'] = $field;
    }

    /**
     * @param Button $action
     */
    public function addAction(ActionInterface $action)
    {
        $this->data['actions'] = $action;
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
