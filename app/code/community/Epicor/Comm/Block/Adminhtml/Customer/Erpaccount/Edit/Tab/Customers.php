<?php

/**
 * Erp account Customers grid
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Customers extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        $this->setDefaultFilter(array('in_customer' => 1));
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_customer') {
            $productIds = $this->_getSelected();
            if (empty($productIds)) {
                $productIds = array(0);
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    public function getGroupCustomers()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
                ->addFieldToFilter('erpaccount_id', $this->getErpCustomer()->getId());
        /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        $collection->addNameToSelect();
        return $collection->getItems();
    }

    public function canShowTab()
    {
        return true;
    }

    public function getTabLabel()
    {
        return 'Customer SKU';
    }

    public function getTabTitle()
    {
        return 'Customer SKU';
    }

    public function isHidden()
    {
        return false;
    }

    public function getErpCustomer()
    {
        if (!$this->_erp_customer) {
            $this->_erp_customer = Mage::registry('customer_erp_account');
        }
        return $this->_erp_customer;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->addNameToSelect();
        $collection->addAttributeToSelect('ecc_master_shopper');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('in_customer', array(
            'header' => Mage::helper('sales')->__('Select'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_customer',
            'values' => $this->_getSelected(),
            'align' => 'center',
            'index' => 'entity_id',
            'sortable' => false,
            'field_name' => 'links[]'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('epicor_comm')->__('Customer'),
            'index' => 'name',
            'filter_index' => 'name'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('epicor_comm')->__('Email'),
            'width' => '150',
            'index' => 'email',
            'filter_index' => 'email'
        ));
         //Hide the Master Shopper Column (If the Account Type is Supplier)
        if(!$this->getErpCustomer()->isTypeSupplier()) {         
            $this->addColumn('ecc_master_shopper', array(
                'header' => Mage::helper('epicor_comm')->__('Master Shopper'),
                'width' => '50',
                'index' => 'ecc_master_shopper',
                'align' => 'center',
                'type' => 'options',
                'options' => array('1' => 'Yes', '0' => 'No')
            ));
        }
        $this->addColumn('action', array(
            'header' => Mage::helper('epicor_comm')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Edit'),
                    'url' => array('base' => 'adminhtml/customer/edit',
                        'params' => array('id' => $this->getRequest()->getParam('id'))
                    ),
                    'field' => 'id'
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addColumn('row_id', array(
            'header' => Mage::helper('catalog')->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'id',
            'width' => 0,
            'editable' => true,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));

        return parent::_prepareColumns();
    }

    protected function _getSelected()
    {   // Used in grid to return selected customers values.
        return array_keys($this->getSelected());
    }

    public function getSelected()
    {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $collection = Mage::getResourceModel('customer/customer_collection');
            if (Mage::helper('epicor_comm')->isModuleEnabled('Epicor_Supplierconnect')) {
                if ($this->getErpCustomer()->isTypeSupplier()) {
                    $collection->addFieldToFilter('supplier_erpaccount_id', $this->getErpCustomer()->getId());
                } else {
                    $collection->addFieldToFilter('erpaccount_id', $this->getErpCustomer()->getId());
                }
            } else {
                $collection->addFieldToFilter('erpaccount_id', $this->getErpCustomer()->getId());
            }

            /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
            foreach ($collection->getAllIds() as $id) {
                $this->_selected[$id] = array('id' => $id);
            }
        }
        return $this->_selected;
    }

    public function setSelected($selected)
    {
        if (!empty($selected)) {
            foreach ($selected as $id) {
                $this->_selected[$id] = array('id' => $id);
            }
        }
    }

    public function getGridUrl()
    {
        $params = array(
            'id' => $this->getErpCustomer()->getId(),
            '_current' => true,
        );
        return $this->getUrl('adminhtml/epicorcomm_customer_erpaccount/customersgrid', $params);
    }

}
