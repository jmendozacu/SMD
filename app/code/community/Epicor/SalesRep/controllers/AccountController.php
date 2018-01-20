<?php

/**
 * Account controller
 *
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_AccountController extends Epicor_SalesRep_Controller_Abstract
{

    /**
     * Index action 
     */
    public function indexAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        /* @var $this Epicor_SalesRep_AccountController */
        $account = Mage::getModel('epicor_salesrep/account')->load($customer->getSalesRepAccountId());
        Mage::register('sales_rep_account', $account);
        Mage::register('sales_rep_account_base', $account);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function masqueradegridAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        /* @var $this Epicor_SalesRep_AccountController */
        $account = Mage::getModel('epicor_salesrep/account')->load($customer->getSalesRepAccountId());
        Mage::register('sales_rep_account', $account);
        Mage::register('sales_rep_account_base', $account);        
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('epicor_salesrep/manage_select_grid')->toHtml());
    }

     public function masqueradepopupAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

}
