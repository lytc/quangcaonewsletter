<?php

class NewsController extends App_Controller
{
    public function indexAction()
    {
        $this->_helper->redirector('list');
    }
    public function listAction()
    {
        $page = (int) $this->getRequest()->getParam('page');
        $identity = Zend_Auth::getInstance()->getIdentity();

        $table = new Application_Model_DbTable_News();
        $select = $table->select()
            ->where('customer_id = ?', $identity['id'])
            ->order('id DESC');
        $paginatorAdapter = new Zend_Paginator_Adapter_DbTableSelect($select);
        $paginator = new Zend_Paginator($paginatorAdapter);

        $paginator->setItemCountPerPage(10)
                    ->setCurrentPageNumber($page);

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->json(array(
                'total'     => $paginator->getTotalItemCount(),
                'items'     => $paginator->getCurrentItems()->toArray()
            ));
        } else {
            $this->view->paginator = $paginator;
        }
    }
}