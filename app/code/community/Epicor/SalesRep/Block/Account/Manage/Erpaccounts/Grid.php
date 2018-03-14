<?php

/**
 * Sales Rep Account ERP Accounts List
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Account_Manage_Erpaccounts_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    private $_selected = array();
    protected $_salesrepChildrenIds;

    public function __construct()
    {
        parent::__construct();
        $this->setId('erpaccountGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);

        $this->setDefaultSort('account_number');
        $this->setDefaultDir('DESC');
        $this->setDefaultFilter(array('selected_erpaccounts' => 1));
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag

        if ($column->getId() == 'selected_erpaccounts') {
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

    /**
     * 
     * @return Epicor_SalesRep_Model_Location
     */
    public function getSalesRepAccount()
    {
        if (!$this->_salesrep) {
            $helper = Mage::helper('epicor_salesrep/account_manage');
            /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

            $salesRep = $helper->getManagedSalesRepAccount();
            $this->_salesrep = $salesRep;
        }

        return $this->_salesrep;
    }
    
    public function getSalesRepAccountChildrenIds()
    {
        if (!$this->_salesrepChildrenIds) {

            $salesRep = $this->getSalesRepAccount();
            $this->_salesrepChildrenIds = $salesRep->getHierarchyChildAccountsIds();
        }

        return $this->_salesrepChildrenIds;
    }

    /**
     * 
     * @return type
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_comm/customer_erpaccount')->getCollection();
        /* @var $collection Epicor_Comm_Model_Mysql4_Customer_Erpaccount_Collection */

        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        $salesRep = $helper->getBaseSalesRepAccount();

        $erpAccounts = $salesRep->getMasqueradeAccountIds();
        $collection->addFieldToFilter('entity_id', array('in' => $erpAccounts));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        if ($helper->canEdit()) {
            $this->addColumn('selected_erpaccounts', array(
                'header' => Mage::helper('sales')->__('Select'),
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'selected_erpaccounts',
                'values' => $this->_getSelected(),
                'align' => 'center',
                'index' => 'entity_id',
                'sortable' => false,
                'field_name' => 'links[]',
                'use_index' => true
            ));
        }

        $this->addColumn('account_number', array(
            'header' => Mage::helper('core')->__('ERP Account Number'),
            'align' => 'left',
            'index' => 'account_number'
        ));

        $this->addColumn('short_code', array(
            'header' => Mage::helper('epicor_comm')->__('Short Code'),
            'index' => 'short_code',
            'filter_index' => 'short_code',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));
        $this->addColumn('erp_account_name', array(
            'header' => Mage::helper('core')->__('Name'),
            'align' => 'left',
            'index' => 'name'
        ));
        
        $this->addColumn('sales_rep_account',array(
            'header' => $this->__('Sales Rep Account'),
            'current_account' => $this->getSalesRepAccount(),
            'account_children_ids' => $this->getSalesRepAccountChildrenIds(),
            'messages' => $this->_messages,
            'renderer' => new Epicor_SalesRep_Block_Account_Manage_Erpaccounts_Renderer_Salesrepaccount(),
            'filter_condition_callback' => array($this, '_salesRepAccountFilter'),
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

            $salesRep = $this->getSalesRepAccount();

            $erpAccountIds = $salesRep->getErpAccountIds();

            foreach ($erpAccountIds as $erpAccountId) {
                $this->_selected[$erpAccountId] = array('entity_id' => $erpAccountId);
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
        return $this->getUrl('*/*/erpaccountsgrid', $params);
    }

    public function getRowUrl($row)
    {
        return null;
    }

    protected function _toHtml()
    {

        $html = parent::_toHtml();

        return $html;
    }
    
    protected function _salesRepAccountFilter($collection, $column){
        if(!$value = $column->getFilter()->getValue()){
            return $this;
        }
        
        $salesRepIds = $this->getSalesRepAccountChildrenIds();
        
        $this->getCollection()
                ->join(array('salesrep_erp' => 'epicor_salesrep/erpaccount'), 'main_table.entity_id = salesrep_erp.erp_account_id', '')
                ->join(array('salesrep' => 'epicor_salesrep/account'), 'salesrep.id = salesrep_erp.sales_rep_account_id', '');
        $this->getCollection()->getSelect()->group('main_table.entity_id');
        
        if (strtolower($value) == strtolower($this->__('This account'))) {
            $salesRepAccount = $this->getSalesRepAccount();
            /* @var $salesRepAccount Epicor_SalesRep_Model_Account */
            $salesRepAccountId = $salesRepAccount->getId();
            $this->getCollection()->addFieldtoFilter('salesrep.id', $salesRepAccountId);
            
        } elseif (strtolower($value) == strtolower($this->__('Child account'))) {
            $childrenSalesRepAccountsIds = $this->getSalesRepAccountChildrenIds();
            $this->getCollection()->addFieldtoFilter('salesrep.id', array('in' => $childrenSalesRepAccountsIds));
            
        } elseif (strtolower($value) == strtolower($this->__('Multiple accounts'))) {
            $childrenSalesRepAccountsIds = $this->getSalesRepAccountChildrenIds();
            $this->getCollection()->addFieldtoFilter('salesrep.id', array('in' => $childrenSalesRepAccountsIds));
            $this->getCollection()->getSelect()->having('COUNT(*) > 1');
            
        } else {
            $this->getCollection()->addFieldtoFilter('salesrep.name', array('like' => "%$value%"));
        }
        
        return $this;
    }

}
