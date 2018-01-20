<?php

class Epicor_Common_Block_Adminhtml_Access_Group_Edit_Tab_Customers extends Mage_Adminhtml_Block_Widget_Grid {

    private $_selected = array();

    public function _construct() {
        $this->setId('catalog_category_products');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        if ($this->getGroup()->getId()) {
            $this->setDefaultFilter(array('customer_in_group' => 1));
        }
    }

    /**
     * Gets the current group data
     * 
     * @return Epicor_Common_Model_Access_Group
     */
    public function getGroup() {
        if (!$this->_accessright) {
            $this->_accessright = Mage::registry('access_group_data');
        }
        return $this->_accessright;
    }

    public function getSelected() {

        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $this->_selected = array();

            foreach ($this->getGroup()->getLinkedCustomers() as $customer) {
                $this->_selected[$customer->getCustomerId()] = array('id' => $customer->getCustomerId());
            }
        }

        return $this->_selected;
    }

    public function _getSelected() {
        return array_keys($this->getSelected());
    }

    /**
     * Sets the currently selected items
     * 
     * @param array $selected
     */
    public function setSelected($selected) {
        if(!empty($selected)) {
            foreach ($selected as $id) {
                $this->_selected[$id] = array('id' => $id);
            }
        }
    }

    protected function _addColumnFilterToCollection($column) {
        // Set custom filter for in category flag
        if ($column->getId() == 'customer_in_group') {
            $customerIds = $this->_getSelected();
            if (empty($customerIds)) {
                $customerIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $customerIds));
            } elseif (!empty($customerIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $customerIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection() {
        //$this->setDefaultFilter(array('customer_in_group'=>1));
        $collection = Mage::getResourceModel('customer/customer_collection')
                ->addNameToSelect();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('customer_in_group', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'field_name' => 'customer[]',
            'name' => 'customer',
            'values' => $this->_getSelected(),
            'align' => 'center',
            'editable' => true,
            'index' => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('customer')->__('Name'),
            'index' => 'name'
        ));
        $this->addColumn('email', array(
            'header' => Mage::helper('customer')->__('Email'),
            'width' => '150',
            'index' => 'email'
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
                ->addFieldToFilter('customer_group_id', array('gt' => 0))
                ->load()
                ->toOptionHash();

        $this->addColumn('group', array(
            'header' => Mage::helper('customer')->__('Group'),
            'width' => '100',
            'index' => 'group_id',
            'type' => 'options',
            'options' => $groups,
        ));

        $this->addColumn('row_id', array(
            'header' => Mage::helper('catalog')->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'entity_id',
            'editable' => true,
            'width' => 0,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));


        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/customergrid', array('id' => $this->getGroup()->getId(), '_current' => true));
    }

    public function getRowUrl($row) {
        return null;
    }
    
}

