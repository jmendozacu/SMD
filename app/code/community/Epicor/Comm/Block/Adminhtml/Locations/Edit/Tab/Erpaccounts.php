<?php

class Epicor_Comm_Block_Adminhtml_Locations_Edit_Tab_Erpaccounts extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('erpaccountGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);

        $this->setDefaultSort('account_number');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        $this->setDefaultFilter(array('selected_erpaccount' => 1));
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag

        if ($column->getId() == 'selected_erpaccount') {
            $ids = $this->_getSelected();
            if (!empty($ids)) {
                if ($column->getFilter()->getValue()) {
                    $this->getCollection()->addFieldToFilter('main_table.entity_id', array('in' => $ids));
                } else {
                    $this->getCollection()->addFieldToFilter('main_table.entity_id', array('nin' => $ids));
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
        return 'Erp Accounts';
    }

    public function getTabTitle()
    {
        return 'Erp Accounts';
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

    /**
     * 
     * @return type
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_comm/customer_erpaccount')->getCollection();
        /* @var $collection Epicor_Comm_Model_Mysql4_Customer_Erpaccount_Collection */

        $location = $this->getLocation();

        $collection->joinLocationLinkInfo($location->getCode());
        $collection->addFieldToFilter('account_type', array('neq' => 'Supplier'));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('selected_erpaccount', array(
            'header' => Mage::helper('sales')->__('Select'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'selected_erpaccount',
            'values' => $this->_getSelected(),
            'align' => 'center',
            'index' => 'entity_id',
            'sortable' => false,
            'field_name' => 'links[]',
            'use_index' => true
        ));

        $this->addColumn('account_number', array(
            'header' => Mage::helper('core')->__('ERP Account Number'),
            'align' => 'left',
            'index' => 'account_number'
        ));

        $this->addColumn('erp_account_name', array(
            'header' => Mage::helper('core')->__('Name'),
            'align' => 'left',
            'index' => 'name'
        ));

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
            $collection = Mage::getModel('epicor_comm/customer_erpaccount')->getCollection();
            /* @var $collection Epicor_Comm_Model_Mysql4_Customer_Erpaccount_Collection */

            $location = $this->getLocation();

            $collection->joinLocationLinkInfo($location->getCode());
            $collection->addFieldToFilter('account_type', array('neq' => 'Supplier'));

            foreach ($collection->getItems() as $erpAccount) {
                /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
                $addSelected = false;
                if ($erpAccount->getLocationLinkType() == Epicor_Comm_Model_Location_Link::LINK_TYPE_INCLUDE) {
                    if ($erpAccount->getLinkType() == $erpAccount->getLocationLinkType()) {
                        $addSelected = true;
                    }
                } else if ($erpAccount->getLocationLinkType() == Epicor_Comm_Model_Location_Link::LINK_TYPE_EXCLUDE) {
                    if ($erpAccount->getLinkType() != $erpAccount->getLocationLinkType()) {
                        $addSelected = true;
                    }
                }

                if ($addSelected) {
                    $this->_selected[$erpAccount->getId()] = array('entity_id' => $erpAccount->getId());
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
        return $this->getUrl('adminhtml/epicorcomm_locations/erpaccountsgrid', $params);
    }

    public function getRowUrl($row)
    {
        return null;
    }

}
