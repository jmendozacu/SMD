<?php
/**
 * AR Payments Admin Screen sales order GRID
 * @category   Epicor
 * @package    Epicor_ErpSimulator
 * @author     Epicor Websales Team
 * 
 * 
 */

class Epicor_Customerconnect_Block_Adminhtml_Arpayments_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('epicor_arpayments_grid');
        $this->setDefaultSort('purchased_on');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/order_collection')->join(array(
            'a' => 'sales/order_address'
        ), 'main_table.entity_id = a.parent_id AND a.address_type != \'billing\'', array(
            'city' => 'city',
            'country_id' => 'country_id'
        ))->join(array(
            'c' => 'customer/customer_group'
        ), 'main_table.customer_group_id = c.customer_group_id', array(
            'customer_group_code' => 'customer_group_code'
        ))->addExpressionFieldToSelect('fullname', 'CONCAT({{customer_firstname}}, \' \', {{customer_lastname}})', array(
            'customer_firstname' => 'main_table.customer_firstname',
            'customer_lastname' => 'main_table.customer_lastname'
        ))->addExpressionFieldToSelect('products', '(SELECT GROUP_CONCAT(\' \', x.name)
                    FROM ' . Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item') . ' x
                    WHERE {{entity_id}} = x.order_id
                        AND x.product_type != \'configurable\')', array(
            'entity_id' => 'main_table.entity_id'
        ))->addAttributeToFilter('sfo.ecc_arpayments_invoice', 1);
        $collection->getSelect()->join(array(
            'sfo' => $collection->getTable('sales/order')
        ), 'main_table.entity_id = sfo.entity_id', array(
            'gor_message' => 'gor_message',
            'gor_status' => 'sfo.status',
            'erp_order_number' => 'sfo.erp_order_number',
            'device_used' => 'sfo.device_used'
        ));
        $collection->getSelect()->join(array(
            'osh' => $collection->getTable('sales/order_status_history')
        ), 'osh.entity_id = (SELECT entity_id FROM ' . $collection->getTable('sales/order_status_history') . ' WHERE parent_id=main_table.entity_id LIMIT 1)', array(
            'ordercomment' => 'osh.comment'
        ));
        
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
    protected function _prepareColumns()
    {
        $helper   = Mage::helper('adminhtml');
        $currency = (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
        $this->addColumn('increment_id', array(
            'header' => $helper->__('AR Payment Reference #'),
            'index' => 'increment_id'
        ));
        $this->addColumnAfter('erp_order_number', array(
            'header' => Mage::helper('sales')->__('ERP Payment Reference #'),
            'index' => 'erp_order_number',
            'filter_index' => 'sfo.erp_order_number'
        ), 'real_order_id');
        //        $this->addColumnAfter('caaps_message', array(
        //            'header' => Mage::helper('sales')->__('Sent to ERP'),
        //            'index' => 'caaps_message',
        //            'width' => '100px',
        //            'filter_index' => 'sfo.caaps_message'
        //                ), 'status');        
        
        $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Paid From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
        ));        
        
        $this->addColumn('purchased_on', array(
            'header' => $helper->__('Paid On'),
            'type' => 'datetime',
            'index' => 'created_at'
        ));
        
        $this->addColumn('fullname', array(
            'header' => $helper->__('Name'),
            'index' => 'fullname',
            'filter_index' => 'CONCAT(customer_firstname, \' \', customer_lastname)'
        ));
        
        
        
        $this->addColumn('grand_total', array(
            'header' => $helper->__('Payments Totals'),
            'index' => 'grand_total',
            'type' => 'currency',
            'currency_code' => $currency
        ));
//        $this->addColumn('order_status', array(
//            'header' => $helper->__('Status'),
//            'index' => 'status',
//            'type' => 'options',
//            'options' => Mage::getSingleton('sales/order_config')->getStatuses()
//        ));
        
        $this->addColumnAfter('ecc_caap_message', array(
            'header' => Mage::helper('sales')->__('Sent to ERP'),
            'index' => 'ecc_caap_message',
            'width' => '100px',
            'filter_index' => 'sfo.ecc_caap_message'
                ), 'status');        
        
        $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/arpayments/view'),
                            'field'   => 'order_id',
                            'data-column' => 'action',
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
        ));        
        
        $this->addExportType('*/*/exportArpaymentsCsv', $helper->__('CSV'));
       // $this->addExportType('*/*/exportArpaymentsExcel', $helper->__('Excel XML'));
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/arpayments/view', array(
                'order_id' => $row->getId()
            ));
        }
        return false;
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array(
            '_current' => true
        ));
    }
}