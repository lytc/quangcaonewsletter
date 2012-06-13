<?php

abstract class App_Crawler_Adapter_Abstract
{
    /**
     * @var string
     */
    protected $_baseUrl;

    /**
     * @var string
     */
    protected $_url;

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param string $url
     * @return string
     * @throws Exception
     */
    public function getPageContent($url)
    {
        if (!preg_match('/^(https?:\/\/)/', $url)) {
            $url = $this->_baseUrl . $url;
        }

        $content = file_get_contents($url);

        if (false === $content) {
            throw new Exception("Cannot get content from $url");
        }

        preg_match('/<body([^>]*)>(.*)<\/body>/s', $content, $match);
        $content = preg_replace('/<script(.*)>(.*)<\/script>/isU', '', $match[2]);
        return $content;
    }
}