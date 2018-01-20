<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Salesreps extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    private $_selected = array();

    public function __construct() {
        parent::__construct();
        $this->setId('salesRepGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        $this->setDefaultFilter(array('selected_salesreps' => 1));
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('epicor_salesrep/account')->getCollection();

        /* @var $collection Epicor_SalesRep_Model_Resource_Account_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _addColumnFilterToCollection($column) {
        // Set custom filter for in salesreps flag
        if ($column->getId() == 'selected_salesreps') {
            $salesrepIds = $this->_getSelected();

            if (empty($salesrepIds)) {
                $salesrepIds = array(0);
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('id', array('in' => $salesrepIds));
            } else {
                if ($salesrepIds) {
                    $this->getCollection()->addFieldToFilter('id', array('nin' => $salesrepIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    public function canShowTab() {
        return true;
    }

    public function getTabLabel() {
        return 'Sales Reps';
    }

    public function getTabTitle() {
        return 'Sales Reps';
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

    protected function _prepareColumns() {
        $this->addColumn('selected_salesreps', array(
            'header' => Mage::helper('core')->__('Select'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'selected_salesreps',
            'values' => $this->_getSelected(),
            'align' => 'center',
            'index' => 'id',
            'filter_index' => 'id',
            'sortable' => false,
            'field_name' => 'links[]'
        ));

        $this->addColumn('sales_rep_id', array(
            'header' => Mage::helper('core')->__('Sales Rep Account Number'),
            'align' => 'left',
            'index' => 'sales_rep_id',
            'filter_index' => 'sales_rep_id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('core')->__('Name'),
            'align' => 'left',
            'index' => 'name',
            'filter_index' => 'name',
        ));

        $this->addColumn('row_id', array(
            'header' => Mage::helper('core')->__('Position'),
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

    protected function _getSelected() {   // Used in grid to return selected customers values.
        return array_keys($this->getSelected());
    }

    public function getSelected() {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {

            $collection = Mage::getModel('epicor_salesrep/erpaccount')->getCollection()
                    ->getSalesRepAccountsByErpAccount($this->getErpCustomer()->getId());
            /* @var $collection Epicor_SalesRep_Model_Resource_Erpaccount_Collection */

            foreach ($collection as $salesRep) {
                $this->_selected[$salesRep->getSalesRepAccountId()] = array('id' => $salesRep->getSalesRepAccountId());
            }
        }
        return $this->_selected;
    }

    public function setSelected($selected) {
        if (!empty($selected)) {
            foreach ($selected as $id) {
                $this->_selected[$id] = array('id' => $id);
            }
        }
    }

    public function getGridUrl() {
        $params = array(
            'id' => $this->getErpCustomer()->getId(),
            '_current' => true,
        );
        return $this->getUrl('adminhtml/epicorcomm_customer_erpaccount/salesrepsgrid', $params);
    }

    public function getRowUrl($row) {
        return null;
    }

}
