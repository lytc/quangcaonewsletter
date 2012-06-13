<?php

class IndexController extends App_Controller
{
    public function indexAction()
    {
        $this->_helper->redirector('create', 'newsletters');
    }


}

