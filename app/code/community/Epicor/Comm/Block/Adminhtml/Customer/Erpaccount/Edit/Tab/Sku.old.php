<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Customersku
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Sku extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function __construct($attributes = array()) {
        parent::__construct($attributes);
        $this->setId('erpaccount_sku');
        $this->setUseAjax(true);
        $this->setDefaultSort('product_sku');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }
    
    public function canShowTab() {
        return true;
    }

    public function getTabLabel() {
        return 'Customer SKU';
    }

    public function getTabTitle() {
        return 'Customer SKU';
    }

    public function isHidden() {
        return false;
    }

    public function getErpCustomer() {
        if (!$this->_erp_customer) {
            $this->_erp_customer = Mage::registry('customer_erp_account');
        }
        return $this->_erp_customer;
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('epicor_comm/customer_sku')
                ->getCollection();

        /* @var $collection Epicor_Comm_Model_Mysql4_Erp_Customer_Sku_Collection */
        $collection->getProductSelect();
        $collection->addFieldToFilter('customer_group_id', $this->getErpCustomer()->getId());
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('product_sku', array(
            'header' => Mage::helper('epicor_comm')->__('Product'),
            'width' => '150',
            'index' => 'product_sku',
            'filter_index' => 'product_table.sku'
        ));
        $this->addColumn('sku', array(
            'header' => Mage::helper('epicor_comm')->__('Customer Sku'),
            'width' => '150',
            'index' => 'sku',
            'filter_index' => 'main_table.sku'
        ));
        $this->addColumn('description', array(
            'header' => Mage::helper('epicor_comm')->__('Description'),
            'index' => 'description'
        ));
        
          $this->addColumn('edit',
            array(
                'header'    =>  Mage::helper('epicor_comm')->__('Edit'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('epicor_comm')->__('Edit'),
                        'url'       => array('base'=> 'adminhtml/epicorcomm_message_ajax/editcpncustomer',
                            'params'=>array('customer'=>$this->getRequest()->getParam('id'))
                            ),
                        'field'     => 'id'
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
        ));
          
          $this->addColumn('delete',
            array(
                'header'    =>  Mage::helper('epicor_comm')->__('Delete'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('epicor_comm')->__('Delete'),
                        'url'       => array('base'=> 'adminhtml/epicorcomm_message_ajax/deletecpncustomer',
                            'params'=>array('customer'=>$this->getRequest()->getParam('id'))
                            ),
                        'field'     => 'id'
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }
    
    public function getGridUrl() {
        $params = array(
            'id' => $this->getErpCustomer()->getId(),
            '_current' => true,
        );
        return $this->getUrl('adminhtml/epicorcomm_customer_erpaccount/skugrid', $params);
    }

}
