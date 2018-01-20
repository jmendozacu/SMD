<?php

class Epicor_Comm_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid {

    protected function _prepareMassaction() {
        parent::_prepareMassaction();

        $accounts = Mage::getModel('epicor_comm/customer_erpaccount')->getCollection()->toOptionArray();

        array_unshift($accounts, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('assign_account', array(
            'label' => Mage::helper('customer')->__('Change ERP Order Status'),
            'url' => $this->getUrl('adminhtml/epicorcomm_sales_order/massAssignErpstatus'),
            'additional' => array(
                'erp_status' => array(
                    'name' => 'erp_status',
                    'type' => 'select',
                    'label' => Mage::helper('customer')->__('Order Sent to Erp'),
                    'values' => array(
			'0' => 'Order Not Sent',
			'1' => 'Order Sent',
			'3' => 'Erp Error',
                    )
                )
            )
        ));

        return $this;
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());

        /* @var $collection Mage_Sales_Model_Resource_Order_Collection */
        $collection->getSelect()->join(array('sfo' => $collection->getTable('sales/order')), 'main_table.entity_id = sfo.entity_id', array('gor_message' => 'gor_message', 'gor_status' => 'sfo.status', 'erp_order_number' => 'sfo.erp_order_number', 'device_used' => 'sfo.device_used'));
        $collection->getSelect()->join(array('osh' => $collection->getTable('sales/order_status_history')), 'osh.entity_id = (SELECT entity_id FROM '.$collection->getTable('sales/order_status_history').' WHERE parent_id=main_table.entity_id LIMIT 1)', array('ordercomment' => 'osh.comment',));

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns() {

        parent::_prepareColumns();
        $this->getColumn('real_order_id')->setFilterIndex('main_table.increment_id');
        $this->getColumn('created_at')->setFilterIndex('main_table.created_at');

        if (!Mage::app()->isSingleStoreMode()) {
            $this->getColumn('store_id')->setFilterIndex('main_table.store_id');
        }

        $this->getColumn('base_grand_total')->setFilterIndex('main_table.base_grand_total');
        $this->getColumn('grand_total')->setFilterIndex('main_table.grand_total');
        $this->getColumn('status')->setFilterIndex('main_table.status');

        $this->addColumnAfter('erp_order_number', array(
            'header' => Mage::helper('sales')->__('ERP Order #'),
            'index' => 'erp_order_number',
            'filter_index' => 'sfo.erp_order_number',
                ), 'real_order_id');

        $this->addColumnAfter('gor_message', array(
            'header' => Mage::helper('sales')->__('Sent to ERP'),
            'index' => 'gor_message',
            'width' => '100px',
            'filter_index' => 'sfo.gor_message'
                ), 'status');

        // Add order comment to grid
        $this->addColumnAfter('ordercomment', array(
            'header' => Mage::helper('sales')->__('Order Comment'),
            'index' => 'ordercomment',
            'filter_index' => 'osh.comment',
                ), 'gor_message');

        if(Mage::getStoreConfig('sales/general/display_device')){
            // Add device used to grid
            $this->addColumnAfter('device_used', array(
                'header'       => Mage::helper('sales')->__('Device Used'),
                'index'        => 'device_used',
                'filter_index' => 'sfo.device_used',
            ),'ordercomment');
        }


        $this->sortColumnsByOrder();
        return $this;
    }

}
