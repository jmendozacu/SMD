<?php

/**
 * 
 * Access rights controller - B2b
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_B2b_Adminhtml_B2b_Access_AdminController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    protected $_aclId = 'customer/access/admin';

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('epicor_common/access/admin')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Access Management'), Mage::helper('adminhtml')->__('Administration'));
        return $this;
    }

    public function portalexcludedelementsAction() {
        $this->loadLayout();
        $elements = $this->getRequest()->getParam('portalelements');

        $this->getLayout()->getBlock('portalelements.grid')->setSelected($elements);
        $this->renderLayout();
    }

    public function portalexcludedelementsgridAction() {
        $this->loadLayout();
        $elements = $this->getRequest()->getParam('portalelements');
        $this->getLayout()->getBlock('portalelements.grid')->setSelected($elements);
        $this->renderLayout();
    }

}
