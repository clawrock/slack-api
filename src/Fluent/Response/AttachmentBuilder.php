<?php

namespace ClawRock\Slack\Fluent\Response;

use ClawRock\Slack\Common\Builder\BuilderInterface;
use ClawRock\Slack\Common\Enum\AttachmentColor;
use ClawRock\Slack\Logic\Response\Attachment\Action;
use ClawRock\Slack\Logic\Response\Attachment\Attachment;
use ClawRock\Slack\Logic\Response\Attachment\Field;

class AttachmentBuilder extends AbstractBuilder implements BuilderInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var MessageDataBuilderInterface
     */
    protected $parent;

    /**
     * AttachmentBuilder constructor.
     * @param MessageDataBuilderInterface|null $parent
     */
    public function __construct(MessageDataBuilderInterface $parent = null)
    {
        $this->parent = $parent;
        $this->setFallback('Default fallback message');
        $this->setCallbackId('default_callback');
    }

    /**
     * @param string|null $fallback
     * @return $this
     */
    public function setFallback($fallback)
    {
        $this->validateType($fallback, ['string', 'null']);
        $this->data['fallback'] = $fallback;
        return $this;
    }

    /**
     * @param $callbackId
     * @return $this
     */
    public function setCallbackId($callbackId)
    {
        $this->validateType($callbackId, ['string', 'null']);
        $this->data['callback_id'] = $callbackId;
        return $this;
    }

    /**
     * @param AttachmentColor|string|null $color
     * @return AttachmentBuilder
     */
    public function setColor($color)
    {
        if ($color instanceof AttachmentColor) {
            $color = $color->getValue();
        } else {
            $this->validateType($color, ['string', 'null']);
            if (is_string($color)) {
                $regex = '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/';
                if (!preg_match($regex, $color)) {
                    throw new \InvalidArgumentException($color . ' is not valid hex color code');
                }
            }
        }

        $this->data['color'] = $color;
        return $this;
    }

    /**
     * @param string|null $pretext
     * @return AttachmentBuilder $this
     */
    public function setPretext($pretext)
    {
        $this->validateType($pretext, ['string', 'null']);
        $this->data['pretext'] = $pretext;
        return $this;
    }

    /**
     * @param string|null $authorName
     * @return AttachmentBuilder $this
     */
    public function setAuthorName($authorName)
    {
        $this->validateType($authorName, ['string', 'null']);
        $this->data['author_name'] = $authorName;
        return $this;
    }

    /**
     * @param url|null $authorLink
     * @return AttachmentBuilder $this
     */
    public function setAuthorLink($authorLink)
    {
        $this->throwExceptionWhenInvalidUrl($authorLink);
        $this->data['author_link'] = $authorLink;
        return $this;
    }

    /**
     * @param $url
     * @throws \InvalidArgumentException
     */
    protected function throwExceptionWhenInvalidUrl($url)
    {
        if (!is_null($url) && filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('Parameter must be valid URL.');
        }
    }

    /**
     * @param string|null $authorIcon
     * @return AttachmentBuilder $this
     */
    public function setAuthorIcon($authorIcon)
    {
        $this->validateType($authorIcon, ['string', 'null']);
        if (is_null($authorIcon)) {
            $this->data['author_icon'] = $authorIcon;
            return $this;
        }
        $this->data['author_icon'] = preg_match('/\:.*?\:/', $authorIcon) ? $authorIcon : ':' . $authorIcon . ':';
        return $this;
    }

    /**
     * @param string|null $title
     * @return AttachmentBuilder $this
     */
    public function setTitle($title)
    {
        $this->validateType($title, ['string', 'null']);
        $this->data['title'] = $title;
        return $this;
    }

    /**
     * @param url|null $titleLink
     * @return AttachmentBuilder $this
     */
    public function setTitleLink($titleLink)
    {
        $this->throwExceptionWhenInvalidUrl($titleLink);
        $this->data['title_link'] = $titleLink;
        return $this;
    }

    /**
     * @param string $text
     * @return AttachmentBuilder $this
     */
    public function setText($text)
    {
        $this->validateType($text, ['string', 'null']);
        $this->data['text'] = $text;
        return $this;
    }

    /**
     * @param string  $title
     * @param string  $value
     * @param boolean $isShort
     * @return AttachmentBuilder $this
     */
    public function addField($title, $value, $isShort)
    {
        $this->data['fields'][] = new Field($title, $value, $isShort);
        return $this;
    }

    /**
     * @param url|null $imageUrl
     * @return AttachmentBuilder $this
     */
    public function setImageUrl($imageUrl)
    {
        $this->throwExceptionWhenInvalidUrl($imageUrl);
        $this->data['image_url'] = $imageUrl;
        return $this;
    }

    /**
     * @param url|null $thumbUrl
     * @return AttachmentBuilder $this
     */
    public function setThumbUrl($thumbUrl)
    {
        $this->throwExceptionWhenInvalidUrl($thumbUrl);
        $this->data['thumb_url'] = $thumbUrl;
        return $this;
    }

    /**
     * @param string|null $footer
     * @return AttachmentBuilder $this
     */
    public function setFooter($footer)
    {
        $this->validateType($footer, ['string', 'null']);
        $this->data['footer'] = $footer;
        return $this;
    }

    /**
     * @param string $footerIcon
     * @return AttachmentBuilder $this
     */
    public function setFooterIcon($footerIcon)
    {
        $this->validateType($footerIcon, ['string', 'null']);
        if (is_null($footerIcon)) {
            $this->data['footer_icon'] = $footerIcon;
            return $this;
        }
        $this->data['footer_icon'] = preg_match('/\:.*?\:/', $footerIcon) ? $footerIcon : ':' . $footerIcon . ':';
        return $this;
    }

    /**
     * @param string $ts
     * @return AttachmentBuilder $this
     */
    public function setTs($ts)
    {
        $this->validateType($ts, ['string', 'null']);
        $this->data['ts'] = $ts;
        return $this;
    }

    /**
     * This method creates attachment and if parent was defined in __construct method
     * then adds itself to parent's attachments list.
     *
     * If parent not set then returns new instance of Attachment object.
     *
     * @return Attachment|ResponseBuilder
     */
    public function end()
    {
        if (!is_null($this->parent)) {
            return $this->parent->addAttachment($this->create());
        }
        return $this->create();
    }

    /**
     * @return Attachment
     */
    public function create()
    {
        return new Attachment($this->data);
    }

    /**
     * @return ActionBuilder
     */
    public function createAction()
    {
        return new ActionBuilder($this);
    }

    /**
     * @param Action $action
     */
    public function addAction(Action $action)
    {
        $this->data['actions'][] = $action;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
