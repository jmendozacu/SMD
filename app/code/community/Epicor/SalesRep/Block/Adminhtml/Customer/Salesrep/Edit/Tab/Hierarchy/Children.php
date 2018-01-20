<?php

class Epicor_SalesRep_Block_Adminhtml_Customer_Salesrep_Edit_Tab_Hierarchy_Children extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    private $_selected = array();

    public function __construct() {
        parent::__construct();
        $this->setId('childrenGrid');
        $this->setGridHeader(Mage::helper('core')->__('Children'));
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        $this->setDefaultFilter(array('selected_children' => 1));
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('epicor_salesrep/account')->getCollection()
                ->addFieldToFilter('id', array('neq' => $this->getSalesRepAccount()->getId()));
        
        /* @var $collection Epicor_SalesRep_Model_Resource_Account_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _addColumnFilterToCollection($column) {
        // Set custom filter for in salesreps flag
        if ($column->getId() == 'selected_children') {
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
        return 'Parents';
    }

    public function getTabTitle() {
        return 'Parents';
    }

    public function isHidden() {
        return false;
    }

    public function getSalesRepAccount() {
        if (!$this->_salesRepAccount) {
            $this->_salesRepAccount = Mage::registry('salesrep_account');
        }
        return $this->_salesRepAccount;
    }

    protected function _prepareColumns() {
        $this->addColumn('selected_children', array(
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

        $this->addColumn('child_sales_rep_id', array(
            'header' => Mage::helper('core')->__('Sales Rep Account Number'),
            'align' => 'left',
            'index' => 'sales_rep_id',
            'filter_index' => 'sales_rep_id',
        ));

        $this->addColumn('child_name', array(
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

            $collection = Mage::getModel('epicor_salesrep/hierarchy')->getCollection()
                    ->addFieldToFilter('parent_sales_rep_account_id', $this->getSalesRepAccount()->getId());
            /* @var $collection Epicor_SalesRep_Model_Resource_Erpaccount_Collection */

            foreach ($collection as $salesRep) {
                $this->_selected[$salesRep->getChildSalesRepAccountId()] = array('id' => $salesRep->getChildSalesRepAccountId());
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
            'id' => $this->getSalesRepAccount()->getId(),
            '_current' => true,
        );
        return $this->getUrl('adminhtml/epicorsalesrep_customer_salesrep/childrengrid', $params);
    }

    public function getRowUrl($row) {
        return null;
    }

}
