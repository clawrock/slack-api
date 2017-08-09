<?php

namespace ClawRock\Slack\Fluent\Response;

use ClawRock\Slack\Common\Enum\ResponseType;
use ClawRock\Slack\Common\SendableInterface;
use ClawRock\Slack\Logic\Message;
use ClawRock\Slack\Logic\MessageData;
use ClawRock\Slack\Logic\Response\Attachment\Attachment;

/**
 * IMPORTANT! Setting fields via constructor, addData, mergeData, mergeDataBuilders will not validate input data.
 *
 * Class ResponseBuilder
 * @package ClawRock\Slack\Fluent\Response
 */
class ResponseBuilder extends AbstractBuilder implements MessageDataBuilderInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var ResponseType
     */
    protected $responseType = null;

    /**
     * @var string
     */
    protected $delayUrl = null;

    /**
     * ResponseBuilder constructor.
     * @param array|null $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     *
     * @param string|null $username
     * @return ResponseBuilder
     */
    public function setUsername($username)
    {
        $this->validateType($username, ['string', 'null']);
        $this->data['username'] = $username;
        return $this;
    }

    public function setResponseType(ResponseType $responseType)
    {
        $this->responseType = $responseType;
    }

    /**
     * List of valid emoji http://www.webpagefx.com/tools/emoji-cheat-sheet/
     *
     * @param string|null $emoji
     * @return ResponseBuilder
     */
    public function setEmoji($emoji)
    {
        $this->validateType($emoji, ['string', 'null']);
        if (is_null($emoji)) {
            $this->data['icon_emoji'] = $emoji;
            return $this;
        }
        $this->data['icon_emoji'] = preg_match('/\:.*?\:/', $emoji) ? $emoji : ':' . $emoji . ':';
        return $this;
    }

    /**
     * @param Attachment $attachment
     * @return ResponseBuilder
     */
    public function addAttachment(Attachment $attachment)
    {
        $this->data['attachments'][] = $attachment;
        return $this;
    }

    /**
     * This will automatically change names like @user into link without a need to explicitly specify the IDs.
     *
     * @param boolean|null $value
     * @return ResponseBuilder
     */
    public function setLinkNames($value)
    {
        if (is_null($value)) {
            $this->data['link_names'] = $value;
            return $this;
        }
        $this->validateType($value, ['boolean', 'null']);
        $this->data['link_names'] = $value ? '1' : '0';
        return $this;
    }

    /**
     * Setting this value to true will automatically parse all link in your message.
     * For example a message with channel name #channel will render as a link.
     *
     * NOTE! This will also link user names, use setLinkNames(false) to disable this behavior.
     *
     * @param boolean|null $value
     * @return ResponseBuilder
     */
    public function setParse($value)
    {
        if (is_null($value)) {
            $this->data['parse'] = $value;
            return $this;
        }
        $this->validateType($value, ['boolean', 'null']);
        $this->data['parse'] = $value ? 'full' : 'none';
        return $this;
    }

    public function delay($url, $delayMessage = '')
    {
        if (ob_get_contents()) {
            ob_end_clean();
        }

        header("Connection: close\r\n");
        header("Content-Encoding: none\r\n");
        ignore_user_abort(true);
        ob_start();
        echo($delayMessage);
        $size = ob_get_length();
        header("Content-Length: $size");

        if (ob_get_contents()) {
            ob_end_flush();
        }

        flush();

        if (is_callable('fastcgi_finish_request')) {
            session_write_close();
            fastcgi_finish_request();
        }

        if (ob_get_contents()) {
            ob_end_clean();
        }

        $this->delayUrl = $url;
    }

    /**
     * @return SendableInterface
     */
    public function createResponseOrDelayedMessage()
    {
        if ($this->isDelayed()) {
            return $this->createDelayedMessage();
        }
        return $this->create()->toResponse();
    }

    /**
     * @return bool
     */
    public function isDelayed()
    {
        return !empty($this->delayUrl);
    }

    /**
     * @return Message
     */
    public function createDelayedMessage()
    {
        if (empty($this->delayUrl)) {
            throw new \RuntimeException('Cannot create delayed message, delayUrl not set');
        }

        return $this->create()->toMessage($this->delayUrl);
    }

    /**
     * @return MessageData
     */
    public function create()
    {
        $messageData = new MessageData();
        $messageData->setContent($this->data);
        if (!is_null($this->responseType)) {
            $messageData->setResponseType($this->responseType);
        }
        return $messageData;
    }

    /**
     * @return AttachmentBuilder
     */
    public function createAttachment()
    {
        return new AttachmentBuilder($this);
    }

    /**
     * @param string|null $text
     * @return ResponseBuilder
     */
    public function addText($text)
    {
        $this->validateType($text, ['string', 'array']);
        if (is_array($text)) {
            foreach ($text as $value) {
                $this->addText($value);
                return $this;
            }
        }
        if (empty($this->data['text'])) {
            $this->data['text'] = '';
        }
        $this->data['text'] .= $text;
        return $this;
    }

    /**
     * @return ResponseBuilder
     */
    public function clearAttachments()
    {
        unset($this->data['attachments']);
        return $this;
    }

    /**
     * @param MessageDataBuilderInterface $messageDataBuilder
     * @param bool                        $priority
     * @param bool                        $concatenateText
     * @return $this
     */
    public function mergeDataBuilder(
        MessageDataBuilderInterface $messageDataBuilder,
        $priority = true,
        $concatenateText = true
    ) {
        $this->data = $this->mergeData($messageDataBuilder->getData(), $priority, $concatenateText)->getData();
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return ResponseBuilder
     */
    public function setData($data)
    {
        $this->validateType($data, ['array']);
        $this->data = $data;
        return $this;
    }

    /**
     * Uses array_replace_recursive to merge and replace provided data.
     * Set priority to true if values from the builder object should override new data.
     *
     * @param array $data
     * @param bool  $priority
     * @param bool  $concatenateText
     * @return ResponseBuilder
     */
    public function mergeData($data, $priority = true, $concatenateText = true)
    {
        if ($concatenateText) {
            $baseText = empty($this->data['text']) ? '' : $this->data['text'];
            $newText  = empty($data['text']) ? '' : $data['text'];
        }
        if ($priority) {
            $this->data = array_replace_recursive($data, $this->data);
            if ($concatenateText) {
                $this->setText($baseText . ' ' . $newText);
            }
            return $this;
        }

        $this->data = array_replace_recursive($this->data, $data);
        if ($concatenateText) {
            $this->setText($newText . ' ' . $baseText);
        }
        return $this;
    }

    /**
     * @param string|null $text
     * @return ResponseBuilder
     */
    public function setText($text)
    {
        $this->validateType($text, ['string', 'null']);
        $this->data['text'] = $text;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setReplaceOriginal($value)
    {
        $this->validateType($value, ['boolean', 'null']);
        $this->data['replace_original'] = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setDeleteOriginal($value)
    {
        $this->validateType($value, ['boolean', 'null']);
        $this->data['delete_original'] = $value;
        return $this;
    }
}
