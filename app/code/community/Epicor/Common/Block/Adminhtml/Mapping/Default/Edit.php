<?php

class Epicor_Common_Block_Adminhtml_Mapping_Default_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function getBackUrl()
    {
        return $this->getUrl('*/*/', $this->_getStoreParams());
    }

    private function _getStoreParams()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        return is_null($storeId) ? array() : array('store' => $storeId);
    }
} 