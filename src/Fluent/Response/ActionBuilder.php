<?php

namespace ClawRock\Slack\Fluent\Response;

use ClawRock\Slack\Common\Builder\BuilderInterface;
use ClawRock\Slack\Common\Enum\ActionStyle;
use ClawRock\Slack\Logic\Response\Attachment\Action;

class ActionBuilder extends AbstractBuilder implements BuilderInterface
{
    /**
     * @var AttachmentBuilder
     */
    protected $parent;

    /**
     * @var Action
     */
    protected $action;

    /**
     * ActionBuilder constructor.
     * @param AttachmentBuilder|null $parent
     */
    public function __construct(AttachmentBuilder $parent = null)
    {
        $this->parent = $parent;
        $this->makeDefault();
        return $this;
    }

    /**
     * Creates new Action object with default values.
     */
    protected function makeDefault()
    {
        $this->action = new Action('Default name', 'Action label', 'button');
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->validateType($name, ['string', 'null']);
        $this->action->setField('name', $name);
        return $this;
    }

    /**
     * @param $text
     * @return $this
     */
    public function setText($text)
    {
        $this->validateType($text, ['string', 'null']);
        $this->action->setField('text', $text);
        return $this;
    }

    /**
     * @param ActionStyle $actionStyle
     * @return $this
     */
    public function setStyle(ActionStyle $actionStyle)
    {
        $this->action->setField('style', $actionStyle->getValue());
        return $this;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->validateType($type, ['string', 'null']);
        $this->action->setField('type', $type);
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->validateType($value, ['string', 'null']);
        $this->action->setField('value', $value);
        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->makeDefault();
        return $this;
    }

    /**
     * @param string|null $title
     * @param string|null $text
     * @param string|null $okText
     * @param string|null $dismissText
     * @return $this
     */
    public function setConfirm($title, $text, $okText, $dismissText)
    {
        $this->validateType($title, ['string', 'null']);
        $this->validateType($text, ['string', 'null']);
        $this->validateType($okText, ['string', 'null']);
        $this->validateType($dismissText, ['string', 'null']);

        $this->action->setField('confirm', [
            'title'        => $title,
            'text'         => $text,
            'ok_text'      => $okText,
            'dismiss_text' => $dismissText
        ]);
        return $this;
    }

    /**
     * @return AttachmentBuilder|Action
     */
    public function end()
    {
        if (!is_null($this->parent)) {
            return $this->parent->addAction($this->create());
        }
        return $this->create();
    }

    /**
     * @return Action
     */
    public function create()
    {
        return $this->action;
    }
}
