<?php

class NewslettersController extends App_Controller
{
    public function indexAction()
    {
        $this->_helper->redirector('list');
    }

    public function listAction()
    {
        $page = (int) $this->getRequest()->getParam('page');
        $identity = Zend_Auth::getInstance()->getIdentity();

        $table = new Application_Model_DbTable_Newsletter();
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

    public function viewAction()
    {
        $id = $this->getRequest()->getParam('id');
        $newsletterTable = new Application_Model_DbTable_Newsletter();

        $row = $newsletterTable->find($id)->current();

        if (!$row) {
            throw new Zend_Controller_Action_Exception("Page not found", 404);
        }

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->getResponse()->setBody($row->content);
    }

    public function createAction()
    {
        // action body
    }

    public function addAction()
    {
        $name = $this->getRequest()->getParam('name');

        $content = $this->getRequest()->getParam('content');
        $content = preg_replace('/<script(.*)>(.*)<\/script>/isU', '', $content);
        $content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">' . $content . '</html>';

        $identity = Zend_Auth::getInstance()->getIdentity();

        $data = array(
            'customer_id' => $identity['id'],
            'name' => $name,
            'content' => $content,
            'created_time' => new Zend_Db_Expr('NOW()')
        );

        $newsletterTable = new Application_Model_DbTable_Newsletter();
        $row = $newsletterTable->createRow($data);
        $row->save();

        $this->_helper->json(array('success' => true, 'data' => $row->toArray()));
    }
}

