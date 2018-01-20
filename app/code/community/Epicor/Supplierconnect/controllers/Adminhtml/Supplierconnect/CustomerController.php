<?php

/**
 * Response SUCO - Upload Supplier Connect Users 
 *
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
include_once("Mage/Adminhtml/controllers/CustomerController.php");

class Epicor_Supplierconnect_Adminhtml_Supplierconnect_CustomerController extends Mage_Adminhtml_CustomerController
{
    public function listaccountsAction()
    {
        if ($this->getRequest()->get('grid')) {
            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('supplierconnect/adminhtml_customer_supplieraccount_attribute_grid')->toHtml()
            );
        } else {
            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('supplierconnect/adminhtml_customer_supplieraccount_attribute')->toHtml()
            );
        }
    }

}
