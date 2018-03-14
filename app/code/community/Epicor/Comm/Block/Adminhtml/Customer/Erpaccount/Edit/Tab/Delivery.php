<?php

/**
 * Erp account Allowed Delivery Methods grid
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Delivery extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('erpaccount_delivery');
        $this->setUseAjax(true);
        $this->setDefaultFilter(array('code' => 1));
        $this->setSaveParametersInSession(true);

    }
    public function getErpCustomer()
    {
        if (!$this->_erp_customer) {
            if (Mage::registry('customer_erp_account')) {
                $this->_erp_customer = Mage::registry('customer_erp_account');
            } else {
                $this->_erp_customer = Mage::getModel('epicor_comm/customer_erpaccount')->load($this->getRequest()->getParam('id'));
            }
        }
        return $this->_erp_customer;
    }
    public function canShowTab()
    {
        return true;
    }

    public function getTabLabel()
    {
        return 'Delivery';
    }

    public function getTabTitle()
    {
        return 'Delivery';
    }

    public function isHidden()
    {
        return false;
    }


    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_comm/erp_mapping_shippingmethod')->getCollection();             
      $this->setCollection($collection);
        return parent::_prepareCollection();
    }
   protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'code') {

            $ids = $this->_getSelected();
            if (empty($ids)) {
                $ids = array();
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('shipping_method_code', array('in' => $ids));
            } else {
                if ($ids) {
                    $this->getCollection()->addFieldToFilter('shipping_method_code', array('nin' => $ids));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _getSelected()
    {   // Used in grid to return selected customers values.
        return array_keys($this->getSelected());
    }

    public function getSelected()
    {
        $allowed = array();
        $excluded = array();
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $erpAccount = $this->getErpCustomer();
            /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */

            $allowed = unserialize($erpAccount->getAllowedDeliveryMethods());
            $excluded = unserialize($erpAccount->getAllowedDeliveryMethodsExclude());

            if(!(empty($allowed) && empty($excluded))){

                $include = !empty($allowed) ? 'Y' : 'N';
                if($include == 'Y'){
                    foreach ($allowed as $deliveryCode) {
                        $this->_selected[$deliveryCode] = array('shipping_method_code' => $deliveryCode);
                    }        
                }else{
                    foreach ($excluded as $deliveryCode) {
                        $this->_selected[$deliveryCode] = array('shipping_method_code' => $deliveryCode);
                    }
                }
            }
            
        }

        return $this->_selected;
    }

    public function setSelected($selected)
    {
        if (!empty($selected)) {
            foreach ($selected as $code) {
                $this->_selected[$code] = array('shipping_method_code' => $code);
            }
        }
    }

    protected function _prepareColumns()
    {
        $this->addColumn('code', array(
            'header' => Mage::helper('epicor_lists')->__('Select'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'code',
            'align' => 'center',
            'index' => 'shipping_method_code',
            'sortable' => false,
            'field_name' => 'links[]',
            'values' => $this->_getSelected(),
            'use_index' => true

            ));
              $this->addColumn('shipping_method_code', array(
          'header'    => Mage::helper('epicor_comm')->__('Shipping Method Code'),
          'align'     =>'left',
          'index'     => 'shipping_method_code',
     ));
      
      $this->addColumn('delivery_code', array(
          'header'    => Mage::helper('epicor_comm')->__('Erp Code Value'),
          'align'     => 'left',
          'index'     => 'erp_code',
      ));

        $this->addColumn('row_id', array(
            'header' => Mage::helper('catalog')->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'shipping_method_code',
            'width' => 0,
            'editable' => true,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));

        return parent::_prepareColumns();
    }

public function getGridUrl()
    {
        $params = array(
            'id' => $this->getErpCustomer()->getId(),
            '_current' => true,
            'ajax' => true
        );
        return $this->getUrl('adminhtml/epicorcomm_customer_erpaccount/deliverygrid', $params);
    }

}
