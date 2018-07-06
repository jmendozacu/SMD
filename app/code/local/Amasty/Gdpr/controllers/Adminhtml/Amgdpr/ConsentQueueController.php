<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Adminhtml_Amgdpr_ConsentQueueController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('customer/amgdpr/consent_queue');
        $this->_title($this->__('Customers Consents Email Queue'));
        $this->_addBreadcrumb($this->__('Customers Consents Email Queue'), $this->__('Customers Consents Email Queue'));
        $block = $this->getLayout()->createBlock('amgdpr/adminhtml_consentQueue');
        $this->_addContent($block);
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/amgdpr/consent_queue');
    }
}