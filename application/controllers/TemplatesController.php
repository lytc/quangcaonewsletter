<?php

class TemplatesController extends App_Controller
{
    public function getAction()
    {
        $name = $this->getRequest()->getParam('name');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setBody(file_get_contents(APPLICATION_PATH . "/templates/phapluattp1.html"));
    }
}