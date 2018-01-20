<?php

/**
 * 
 * Customer grid for customer selector input
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Customer_Attribute_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setRowClickCallback('accountSelector.selectAccount.bind(accountSelector)');
        $this->setRowInitCallback('accountSelector.updateWrapper.bind(accountSelector)');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection');
        /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        $erpaccountTable = $collection->getTable('epicor_comm/customer_erpaccount');
        $salesRepTable = $collection->getTable('epicor_salesrep/account');

        $collection->addNameToSelect();
        $collection->addAttributeToSelect('email');
        $collection->addAttributeToSelect('ecc_master_shopper');
        $collection->addAttributeToSelect('created_at');
        $collection->addAttributeToSelect('group_id');
        $collection->addAttributeToSelect('previous_erpaccount');
        $collection->addAttributeToSelect('erpaccount_id', 'left');
        $collection->addAttributeToSelect('sales_rep_account_id', 'left');
        $collection->addAttributeToSelect('ecc_erp_account_type', 'left');
        $collection->joinTable(array('cc' => $erpaccountTable), 'entity_id=erpaccount_id', array('customer_erp_code' => 'erp_code', 'customer_company' => 'company', 'customer_short_code' => 'short_code'), null, 'left');

        $collection->addAttributeToSelect('supplier_erpaccount_id', 'left');
        $collection->joinTable(array('ss' => $erpaccountTable), 'entity_id=supplier_erpaccount_id', array('supplier_erp_code' => 'erp_code', 'supplier_company' => 'company', 'supplier_short_code' => 'short_code'), null, 'left');
        $collection->joinTable(array('sr' => $salesRepTable), 'id=sales_rep_account_id', array('sales_rep_id' => 'sales_rep_id'), null, 'left');
        $collection->addExpressionAttributeToSelect('joined_company', "IF(cc.company IS NOT NULL, cc.company, IF(ss.company IS NOT NULL, ss.company, ''))", 'erpaccount_id');
        $collection->addExpressionAttributeToSelect('joined_short_code', "IF(sr.sales_rep_id IS NOT NULL, sr.sales_rep_id, IF(cc.short_code IS NOT NULL, cc.short_code, IF(ss.short_code IS NOT NULL, ss.short_code, '')))", 'erpaccount_id');
        $collection->addExpressionAttributeToSelect('erp_account_type', "IF(cc.erp_code IS NOT NULL, 'Customer', IF(ss.erp_code IS NOT NULL, 'Supplier', IF(at_sales_rep_account_id.value IS NOT NULL, 'Sales Rep', 'Guest')))", 'erpaccount_id');
        $collection->addAttributeToFilter('ecc_erp_account_type', array('neq' => 'supplier'));
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    
    protected function _prepareColumns()
    {   
        $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name',
            'column_css_class'     => 'return-label',
        ));
        
        $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'index'     => 'email'
        ));
        
        $this->addColumn('erp_account_type', array(
            'header' => Mage::helper('epicor_comm')->__('Account Type'),
            'index' => 'erp_account_type',
            'filter_index' => 'ecc_erp_account_type',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Customer_Grid_Renderer_Accounttype(),
            'type'      => 'options',
            'options' => Mage::helper('epicor_common/account_selector')->getAccountTypeNames(),   
        ));
        
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('customer')->__('Website'),
                'align'     => 'left',
                'width'     => '160px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }
        
        $this->addColumn('customer_short_code', array(
            'header' => Mage::helper('epicor_comm')->__('Short Code'),
            'index' => 'customer_short_code',
            'width' => '90'
        ));

        $this->getColumn('customer_short_code')->setIndex('joined_short_code');

        $this->addColumn('ecc_master_shopper', array(
            'header' => Mage::helper('epicor_comm')->__('Master Shopper'),
            'width' => '50',
            'index' => 'ecc_master_shopper',
            'align' => 'center',
            'type' => 'options',
            'options' => array('1' => 'Yes', '0' => 'No')
        ));        
        
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
