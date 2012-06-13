<?php

class AdsController extends App_Controller
{
    public function indexAction()
    {
        $this->_helper->redirector('list');
    }

    public function listAction() {
        $page = (int) $this->getRequest()->getParam('page');
        $identity = Zend_Auth::getInstance()->getIdentity();

        $table = new Application_Model_DbTable_Ads();
        $select = $table->select()
            ->where('customer_id = ?', $identity['id'])
            ->order('id DESC');

        $paginatorAdapter = new Zend_Paginator_Adapter_DbTableSelect($select);
        $paginator = new Zend_Paginator($paginatorAdapter);
        $paginator->setItemCountPerPage(20)
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

    public function addAction()
    {
        $content = $this->getRequest()->getParam('content');
        $content = preg_replace('/<script(.*)>(.*)<\/script>/isU', '', $content);

        $identity = Zend_Auth::getInstance()->getIdentity();

        $table = new Application_Model_DbTable_Ads();
        $data = array(
            'customer_id'   => $identity['id'],
            'content'       => $content,
            'created_time'  => new Zend_Db_Expr('NOW()')
        );
        $table->createRow($data)->save();

        $this->_helper->redirector('index');
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $identity = Zend_Auth::getInstance()->getIdentity();

        $table = new Application_Model_DbTable_Ads();
        $table->delete(array('id = ?' => $id, 'customer_id = ?' => $identity['id']));

        $this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
    }
}