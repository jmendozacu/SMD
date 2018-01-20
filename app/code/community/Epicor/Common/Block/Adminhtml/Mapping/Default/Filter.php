<?php

class Epicor_Common_Block_Adminhtml_Mapping_Default_Filter extends Mage_Adminhtml_Block_Widget_Grid {

    protected function _prepareCollection()
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('store_id', $this->_getStoreParam());
        return parent::_prepareCollection();
    }

    protected function _getStoreParam()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return $storeId;
    }

}