<?php

/**
 * 
 * Customer Access Groups controller
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
include_once('Mage' . DS . 'Adminhtml' . DS . 'controllers' . DS . 'CustomerController.php');

class Epicor_Common_Adminhtml_Epicorcommon_Customer_AccessController extends Mage_Adminhtml_CustomerController {

    public function groupAction() {

        $this->_initCustomer();
        $this->loadLayout();
        $this->getLayout()->getBlock('groups.grid')
                ->setSelected($this->getRequest()->getPost('groups', null));
        $this->renderLayout();
    }

    public function groupgridAction() {
        $this->_initCustomer();
        $this->loadLayout();
        $this->getLayout()->getBlock('groups.grid')
                ->setSelected($this->getRequest()->getPost('groups', null));
        $this->renderLayout();
    }

}