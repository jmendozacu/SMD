<?php

/**
 * Display list grid in customer tab
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
include_once("Mage/Adminhtml/controllers/CustomerController.php");

class Epicor_Lists_Adminhtml_Epicorlists_CustomerController extends Mage_Adminhtml_CustomerController
{

    public function listsAction()
    {

        $this->_initCustomer();
        $this->loadLayout();
        $this->getLayout()->getBlock('customer_lists_grid')
                ->setSelected($this->getRequest()->getPost('lists', null));
        $this->renderLayout();
    }

    public function listsgridAction()
    {
        $this->_initCustomer();
        $customers = $this->getRequest()->getParam('lists');
        $this->loadLayout();
        $this->getLayout()->getBlock('customer_lists_grid')->setSelected($customers);
        $this->renderLayout();
    }

}
