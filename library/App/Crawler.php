<?php
class App_Crawler
{
    /**
     * @var App_Crawler_Adapter_Abstract
     */
    protected $_adapter;

    /**
     * @param string|App_Crawler_Adapter_Abstract $adapter
     */
    public function __construct($adapter)
    {
        if (!($adapter instanceof App_Crawler_Adapter_Abstract)) {
            $adapter = ucfirst($adapter);
            $adapter = "App_Crawler_Adapter_$adapter";

            if (!class_exists($adapter)) {
                throw new Exception("Crawler adapter $adapter does not exist");
            }

            $adapter = new $adapter;
        }

        if (!($adapter instanceof App_Crawler_Adapter_Abstract)) {
            throw new Exception("Crawler must be an instanceof App_Crawler_Adapter_Abstract");
        }

        $this->_adapter = $adapter;
    }

    /**
     * @return App_Crawler_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * @param string $url
     * @return string
     */
    public function getPageContent($url)
    {
        return $this->_adapter->getPageContent($url);
    }
}