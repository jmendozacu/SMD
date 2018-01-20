<?php

/**
 * 
 * ERP Account grid for erp account selector input
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Attribute_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('erp_account_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setRowClickCallback('accountSelector.selectAccount.bind(accountSelector)');
        $this->setRowInitCallback('accountSelector.updateWrapper.bind(accountSelector)');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_comm/customer_erpaccount')->getCollection();

        $data = $this->getRequest()->getParams();

        if ($data['field_id'] != 'erp_account_id') {
            $this->addAccountTypeFilter($collection, $data['field_id']);
        }
        /* @var $collection Epicor_Comm_Model_Mysql4_Customer_Erpaccount_Collection */

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function addAccountTypeFilter(&$collection, $fieldName)
    {
        $types = array('B2B', 'B2C');
        $collection->addFieldToFilter('account_type', array('in' => $types));

        if (strpos($fieldName, 'default') === false && strpos($fieldName, 'scheduledmsqcustomer') === false && strpos($fieldName, 'msq_after_stk_customer') === false) {
            $allStoresDefaultErpAccounts = Mage::getModel('core/config_data')->getCollection()
                    ->addFieldToFilter('path', array('eq' => 'customer/create_account/default_erpaccount')); // get default erp accounts for all stores
            $allDefaultErpValues = array();
            foreach ($allStoresDefaultErpAccounts as $eachStoreErpAccounts) {
                $allDefaultErpValues[] = $eachStoreErpAccounts->getValue();
            }

            // only include if not default erp account
            $collection->addFieldToFilter('entity_id', array('nin' => $allDefaultErpValues));
        }
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();

//        $this->addColumn('erp_code', array(
//            'header' => Mage::helper('epicor_comm')->__('ERP Customer Code'),
//            'index' => 'erp_code',
//            'width' => '20px',
//            'filter_index' => 'erp_code'
//        ));

        $this->addColumn('rowdata', array(
            'header' => Mage::helper('epicor_comm')->__(''),
            'align' => 'left',
            'width' => '1',
            'name' => 'rowdata',
                'filter'    => false,
                'sortable'  => false,
            'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_rowdata',
            'column_css_class'=> 'no-display',
            'header_css_class'=> 'no-display',
        ));
        
        $this->addColumn('account_type', array(
            'header' => Mage::helper('epicor_comm')->__('ERP Account Type'),
            'index' => 'account_type',
            'width' => '20px',
            'filter_index' => 'account_type',
        ));

        $this->addColumn('company', array(
            'header' => Mage::helper('epicor_comm')->__('Company'),
            'index' => 'company',
            'width' => '20px',
            'filter_index' => 'company'
        ));
        $this->addColumn('short_code', array(
            'header' => Mage::helper('epicor_comm')->__('Short Code'),
            'index' => 'short_code',
            'width' => '20px',
            'filter_index' => 'short_code'
        ));
        $this->addColumn('account_number', array(
            'header' => Mage::helper('epicor_comm')->__('Account Number'),
            'index' => 'account_number',
            'width' => '20px',
            'filter_index' => 'account_number'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('epicor_comm')->__('Name'),
            'index' => 'name',
            'filter_index' => 'name',
            'column_css_class'     => 'return-label',
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $row->getId();
    }

    public function getGridUrl()
    {
        $data = $this->getRequest()->getParams();
        return $this->getUrl('*/*/*', array('grid' => true, 'field_id' => $data['field_id']));
    }

}
