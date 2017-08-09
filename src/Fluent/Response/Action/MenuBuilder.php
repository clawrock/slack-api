<?php

namespace ClawRock\Slack\Fluent\Response\Action;

use ClawRock\Slack\Common\Enum\ActionType;
use ClawRock\Slack\Common\Enum\MenuSource;
use ClawRock\Slack\Fluent\Response\AbstractBuilder;
use ClawRock\Slack\Fluent\Response\AttachmentBuilder;
use ClawRock\Slack\Logic\Response\Attachment\Action;

class MenuBuilder extends AbstractBuilder
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
     * @var array
     */
    protected $options;

    /**
     * @var MenuSource
     */
    protected $menuSource;

    /**
     * MenuBuilder constructor.
     * @param AttachmentBuilder|null $parent
     */
    public function __construct(AttachmentBuilder $parent = null)
    {
        $this->parent = $parent;
        $this->makeDefault();
        $this->setSource(MenuSource::STATIC_SOURCE());
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

    public function addOption($text, $value)
    {
        $this->validateType($text, ['string']);
        $this->validateType($value, ['string']);
        $this->options[] = ['text' => $text, 'value' => $value];
        return $this;
    }

    public function removeOption($optionText)
    {
        $this->validateType($optionText, ['string']);
        array_filter($this->options, function (array $array) use ($optionText) {
            return $array['text'] === $optionText;
        });
        return $this;
    }

    public function clearOptions()
    {
        $this->options = [];
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
     * @return $this
     */
    public function reset()
    {
        $this->makeDefault();
        return $this;
    }

    /**
     * @param MenuSource $menuSource
     * @return $this
     */
    public function setSource(MenuSource $menuSource)
    {
        $this->action->setField('data_source', $menuSource->getValue());
        $this->menuSource = $menuSource->getValue();
        return $this;
    }

    /**
     * @return AttachmentBuilder|Action
     */
    public function end()
    {
        $this->setOptionsToActionObject();
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
        $this->setOptionsToActionObject();
        return $this->action;
    }

    protected function setOptionsToActionObject()
    {
        if ($this->menuSource === MenuSource::STATIC_SOURCE) {
            $this->action->setField('options', $this->options);
        }
    }

    /**
     * Creates new Action object with default values.
     */
    protected function makeDefault()
    {
        $this->options    = [];
        $this->menuSource = MenuSource::STATIC_SOURCE;
        $this->action     = new Action(ActionType::MENU(), 'Default menu name', 'Default menu text');
    }
}
