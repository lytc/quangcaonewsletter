<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initPaginationControl() {
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination-controls.phtml');
    }
}

