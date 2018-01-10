<?php

namespace ClawRock\Slack\Logic\RequestSender;

class FileRequestSender implements RequestSenderInterface
{
    /**
     * @var string
     */
    protected $url;

    protected $filePath;

    /**
     * FileRequestSender constructor.
     * @param string $url
     * @param string $filePath
     */
    public function __construct($url, $filePath)
    {
        $this->setUrl($url);
        if(!is_readable($filePath))
        {
            throw new \InvalidArgumentException('Provided filepath ('.$filePath.') is not readable');
        }
        $this->filePath = $filePath;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return FileRequestSender Returns self instance
     */
    public function setUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException($url . ' is not valid URL');
        }
        $this->url = $url;

        return $this;
    }

    /**
     * @param RequestOptions $options
     * @return bool
     */
    public function send(RequestOptions $options)
    {
        $ch = curl_init();
        $fields = $options->getContent();
        $fields['file'] = new \CURLFile($this->filePath, mime_content_type($this->filePath), 'file');
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [$options->getHeader()]);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }
}
