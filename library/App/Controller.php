<?php

class App_Controller extends Zend_Controller_Action
{
    public function init()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            return;
        }

        //@todo: just hardcode for demo, need to implement
        Zend_Auth::getInstance()->getStorage()->write(array(
            'id'    => 1,
            'name'  => 'phapluattp'
        ));
    }
}