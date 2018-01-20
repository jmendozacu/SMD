<?php

class Epicor_SalesRep_Block_Adminhtml_Customer_Salesrep_Edit_Tab_Salesreps extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('salesrepGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        $this->setDefaultFilter(array('selected_salesreps' => 1));
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'selected_salesreps') {

            $productIds = $this->_getSelected();

            if (!empty($productIds)) {
                if ($column->getFilter()->getValue()) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
                } else {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            } else {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => array(0)));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    public function getGroupCustomers()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
                ->addFieldToFilter('sales_rep_account_id', $this->getSalesRepAccount()->getId());
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
        return 'Sales Reps';
    }

    public function getTabTitle()
    {
        return 'Sales Reps';
    }

    public function isHidden()
    {
        return false;
    }

    /**
     * 
     * @return Epicor_SalesRep_Model_Account
     */
    public function getSalesRepAccount()
    {
        if (!$this->_salesrep) {
            $this->_salesrep = Mage::registry('salesrep_account');
        }

        return $this->_salesrep;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->addNameToSelect();
        $collection->addAttributeToFilter('ecc_erp_account_type', 'salesrep');
        
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('selected_salesreps', array(
            'header' => Mage::helper('epicor_salesrep')->__('Select'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'selected_salesreps',
            'values' => $this->_getSelected(),
            'align' => 'center',
            'index' => 'entity_id',
            'sortable' => false,
            'field_name' => 'links[]',
            'use_index' => true
        ));

        $this->addColumn('salesrep_name', array(
            'header' => Mage::helper('epicor_salesrep')->__('Customer'),
            'width' => '150',
            'index' => 'name',
            'filter_index' => 'name'
        ));

        $this->addColumn('salesrep_email', array(
            'header' => Mage::helper('epicor_salesrep')->__('Email'),
            'width' => '150',
            'index' => 'email',
            'filter_index' => 'email'
        ));
        
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('customer')->__('Website'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }

        $this->addColumn('row_id', array(
            'header' => Mage::helper('catalog')->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'entity_id',
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
            if ($this->getSalesRepAccount()->getId()) {
                $collection = Mage::getResourceModel('customer/customer_collection');

                $collection->addFieldToFilter('sales_rep_account_id', $this->getSalesRepAccount()->getId());

                /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
                foreach ($collection->getAllIds() as $id) {
                    $this->_selected[$id] = array('id' => $id);
                }
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
            'id' => $this->getSalesRepAccount()->getId(),
            '_current' => true,
        );
        return $this->getUrl('adminhtml/epicorsalesrep_customer_salesrep/salesrepsgrid', $params);
    }

}
