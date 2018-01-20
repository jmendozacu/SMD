<?php

class Epicor_Common_Block_Adminhtml_Mapping_Default_Grid extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('epicor_common/mapping/grid.phtml');
    }

    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
            return false;
        }
        return true;
    }

    public function getStoreSwitcherHtml()
    {
        $block = $this->getLayout()->createBlock('adminhtml/store_switcher', 'store_switcher')->setUseConfirm(false);
        $this->setChild('store_switcher', $block);
        return $this->getChildHtml('store_switcher');
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new', $this->_getStoreParams());
    }

    private function _getStoreParams()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        return is_null($storeId) ? array() : array('store' => $storeId);
    }

}