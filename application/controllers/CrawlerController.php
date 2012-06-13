<?php
class CrawlerController extends App_Controller
{
    public function fetchAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();

        $adapter = $identity['name'];
        $crawler = new App_Crawler($adapter);

        if ($this->getRequest()->isPost()) {
            $items = $this->getRequest()->getParam('items');
            foreach ($items as &$item) {
                $item['content'] = $crawler->getPageContent($item['original_url']);
            }
            $result = array('adapter' => $adapter, 'items' => $items);
        } else {
            $result = array(
                'adapter'   => $adapter,
                'content'   => $crawler->getPageContent($crawler->getAdapter()->getUrl())
            );
        }


        $this->_helper->json($result);
    }

    public function saveAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();

        $adapter = $identity['name'];
        $crawler = new App_Crawler($adapter);
        $baseUrl = $crawler->getAdapter()->getBaseUrl();

        $newsTable = new Application_Model_DbTable_News();
        $items = $this->getRequest()->getParam('items');

        foreach ($items as $item) {
            if (!preg_match('/^https?:\/\//', $item['original_url'])) {
                $item['original_url'] = $baseUrl . $item['original_url'];
            }

            if ($newsTable->fetchRow(array('original_url = ?' => $item['original_url']))) {
                continue;
            }

            if (isset($item['thumbnail']) && !preg_match('/^https?:\/\//', $item['thumbnail'])) {
                $item['thumbnail'] = $baseUrl . $item['thumbnail'];
            }

            $item['customer_id'] = $identity['id'];
            $item['created_time'] = new Zend_Db_Expr('NOW()');

            $newsTable->createRow($item)->save();
        }

        $this->_helper->json(array('success' => true));
    }
}