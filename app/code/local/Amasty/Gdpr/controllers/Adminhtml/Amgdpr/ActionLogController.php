<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Adminhtml_Amgdpr_ActionLogController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('customer/amgdpr/action_log');
        $this->_title($this->__('Action Log'));
        $this->_addBreadcrumb($this->__('Action Log'), $this->__('Action Log'));
        $block = $this->getLayout()->createBlock('amgdpr/adminhtml_actionLog');
        $this->_addContent($block);
        $this->renderLayout();
    }

    /**
     * @return mixed
     * @throws Varien_Exception
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/amgdpr/action_log');
    }
}
