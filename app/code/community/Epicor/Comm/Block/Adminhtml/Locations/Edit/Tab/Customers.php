<?php

class Epicor_Comm_Block_Adminhtml_Locations_Edit_Tab_Customers extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);

        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        $this->setDefaultFilter(array('selected_customer' => 1));
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag

        if ($column->getId() == 'selected_customer') {
            $ids = $this->_getSelected();
            if (empty($ids)) {
                $ids = array(0);
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $ids));
            } else {
                if ($ids) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $ids));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    public function canShowTab()
    {
        return true;
    }

    public function getTabLabel()
    {
        return 'Customers';
    }

    public function getTabTitle()
    {
        return 'Customers';
    }

    public function isHidden()
    {
        return false;
    }

    /**
     * 
     * @return Epicor_Comm_Model_Location
     */
    public function getLocation()
    {
        if (!$this->_location) {
            $this->_location = Mage::registry('location');
        }
        return $this->_location;
    }

    protected function _prepareCollection()
    {
        /* @var $helper Epicor_Comm_Helper_Locations */
        $helper = Mage::helper('epicor_comm/locations');
        $this->setCollection($helper->getCustomersCollectionForLocation($this->getLocation()->getCode()));
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('selected_customer', array(
            'header' => Mage::helper('sales')->__('Select'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'selected_customer',
            'values' => $this->_getSelected(),
            'align' => 'center',
            'index' => 'entity_id',
            'sortable' => false,
            'field_name' => 'links[]',
            'use_index' => true
        ));

        $this->addColumn('customer_name', array(
            'header' => Mage::helper('epicor_comm')->__('Customer'),
            'width' => '150',
            'index' => 'name',
            'filter_index' => 'name'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('epicor_comm')->__('Email'),
            'width' => '150',
            'index' => 'email',
            'filter_index' => 'email'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header' => Mage::helper('customer')->__('Website'),
                'align' => 'center',
                'width' => '80px',
                'type' => 'options',
                'options' => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index' => 'website_id',
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

            /* @var $helper Epicor_Comm_Helper_Locations */
            $helper = Mage::helper('epicor_comm/locations');

            $locationCode = $this->getLocation()->getCode();
            $customers = $helper->getCustomersCollectionForLocation($locationCode);


            foreach ($customers->getItems() as $customer) {
                /* @var $customer Epicor_Comm_Model_Customer */
                $linkType = $customer->getEccLocationLinkType();
                if (is_null($linkType) || $customer->isLocationAllowed($locationCode)) {
                    $this->_selected[$customer->getEntityId()] = array('id' => $customer->getEntityId());
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
            'id' => $this->getLocation()->getId(),
            '_current' => true,
        );
        return $this->getUrl('adminhtml/epicorcomm_locations/customersgrid', $params);
    }

    public function getRowUrl($row)
    {
        return null;
    }

}
