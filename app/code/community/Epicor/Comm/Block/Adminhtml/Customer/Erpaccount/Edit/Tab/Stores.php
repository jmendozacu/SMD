<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Stores extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('storeGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);

        $this->setDefaultSort('website_title');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        $this->setDefaultFilter(array('selected_store' => 1));
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag

        if ($column->getId() == 'selected_store') {
            $storeIds = $this->_getSelected();
            if (empty($storeIds)) {
                $storeIds = array(0);
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('store_table.store_id', array('in' => $storeIds));
            } else {
                if ($storeIds) {
                    $this->getCollection()->addFieldToFilter('store_table.store_id', array('nin' => $storeIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    public function getGroupStores()
    {
        $customer = $this->getErpCustomer();
        /* @var $customer Epicor_Comm_Model_Customer_Erpaccount */
        return $customer->getValidStores();
    }

    public function canShowTab()
    {
        return true;
    }

    public function getTabLabel()
    {
        return 'Stores';
    }

    public function getTabTitle()
    {
        return 'Stores';
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
        $collection = Mage::getModel('core/website')
                ->getCollection()
                ->joinGroupAndStore();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        if (!Mage::getStoreConfigFlag('Epicor_Comm/brands/erpaccount')) {
            $this->addColumn('selected_store', array(
                'header' => Mage::helper('sales')->__('Select'),
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'selected_store',
                'values' => $this->_getSelected(),
                'align' => 'center',
                'index' => 'store_id',
                'filter_index' => 'store_table.store_id',
                'sortable' => false,
                'field_name' => 'links[]'
            ));
        } else {
            $this->addColumn('selected_store', array(
                'header' => Mage::helper('sales')->__('Select'),
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'selected_store',
                'values' => $this->_getSelected(),
                'align' => 'center',
                'index' => 'store_id',
                'filter_index' => 'store_table.store_id',
                'sortable' => false,
                'field_name' => 'links[]',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display'
            ));
        }

        $this->addColumn('website_title', array(
            'header' => Mage::helper('core')->__('Website Name'),
            'align' => 'left',
            'index' => 'name',
            'filter_index' => 'main_table.name',
        ));

        $this->addColumn('group_title', array(
            'header' => Mage::helper('core')->__('Store Name'),
            'align' => 'left',
            'index' => 'group_title',
            'filter_index' => 'group_table.name',
        ));

        $this->addColumn('store_title', array(
            'header' => Mage::helper('core')->__('Store View Name'),
            'align' => 'left',
            'index' => 'store_title',
            'filter_index' => 'store_table.name',
        ));

        $this->addColumn('row_id', array(
            'header' => Mage::helper('catalog')->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'store_id',
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

            $customer = $this->getErpCustomer();
            /* @var $customer Epicor_Comm_Model_Customer_Erpaccount */

            foreach ($customer->getValidStores() as $store) {
                $this->_selected[$store] = array('id' => $store);
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
        return $this->getUrl('adminhtml/epicorcomm_customer_erpaccount/storesgrid', $params);
    }

    public function getRowUrl($row)
    {
        return null;
    }

}
