<?php

namespace ClawRock\Slack\Fluent\Response;

use ClawRock\Slack\Common\Builder\BuilderInterface;
use ClawRock\Slack\Common\Enum\ResponseType;
use ClawRock\Slack\Logic\Message;
use ClawRock\Slack\Logic\MessageData;
use ClawRock\Slack\Logic\Response\Attachment\Attachment;

interface MessageDataBuilderInterface extends BuilderInterface
{
    /**
     * @param Attachment $attachment
     * @return MessageDataBuilderInterface
     */
    public function addAttachment(Attachment $attachment);

    /**
     * Deletes all attachments.
     * @return MessageDataBuilderInterface
     */
    public function clearAttachments();

    /**
     * Allows to set response type (ephemeral/in_channel)
     * @param ResponseType $responseType
     * @return MessageDataBuilderInterface
     */
    public function setResponseType(ResponseType $responseType);

    /**
     * @param string|null $username
     * @return MessageDataBuilderInterface
     */
    public function setUsername($username);

    /**
     * @param string|null $emoji
     * @return MessageDataBuilderInterface
     */
    public function setEmoji($emoji);

    /**
     * @param bool|null $value
     * @return MessageDataBuilderInterface
     */
    public function setLinkNames($value);

    /**
     * @param bool|null $value
     * @return MessageDataBuilderInterface
     */
    public function setParse($value);

    /**
     * @return AttachmentBuilder
     */
    public function createAttachment();

    /**
     * @param string|null $text
     * @return MessageDataBuilderInterface
     */
    public function addText($text);

    /**
     * @param string|null $text
     * @return MessageDataBuilderInterface
     */
    public function setText($text);

    /**
     * @return array
     */
    public function getData();

    /**
     * @param $body
     * @return MessageDataBuilderInterface
     */
    public function setData($body);

    /**
     * Priority allows to set which key will be overridden.
     *
     * @param      $data
     * @param bool $priority If true then object's key are more important.
     * @return MessageDataBuilderInterface
     */
    public function mergeData($data, $priority);

    /**
     * Priority allows to set which key will be overridden.
     *
     * @param MessageDataBuilderInterface $builder
     * @param  bool                       $priority If true then base object's key are more important.
     * @return MessageDataBuilderInterface
     */
    public function mergeDataBuilder(MessageDataBuilderInterface $builder, $priority);

    /**
     * Slack API docs:
     * Used only when creating messages in response to a button action invocation.
     * When set to true, the inciting message will be replaced by this message you're providing.
     * When false, the message you're providing is considered a brand new message.
     *
     * @param bool
     * @return MessageDataBuilderInterface
     */
    public function setReplaceOriginal($value);

    /**
     * Slack API docs:
     * Used only when creating messages in response to a button action invocation.
     * When set to true, the inciting message will be deleted and if a message is provided,
     * it will be posted as a brand new message.
     *
     * @param bool
     * @return MessageDataBuilderInterface
     */
    public function setDeleteOriginal($value);

    /**
     * Creates instance of MessageData
     *
     * @return MessageData
     */
    public function create();

    /**
     * @return bool
     */
    public function isDelayed();

    /**
     * @param string $url
     * @param string $delayMessage
     * @return mixed
     */
    public function delay($url, $delayMessage);

    /**
     * @return Message
     */
    public function createDelayedMessage();
}
