<?php

/**
 * 
 * ERP Account grid for erp account selector input
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Adminhtml_Customer_Salesrep_Popup_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('salesrepaccount_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setRowClickCallback('accountSelector.selectAccount.bind(accountSelector)');
        $this->setRowInitCallback('accountSelector.updateWrapper.bind(accountSelector)');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('epicor_salesrep/account')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        parent::_prepareColumns();

//        $this->addColumn('erp_code', array(
//            'header' => Mage::helper('epicor_comm')->__('ERP Customer Code'),
//            'index' => 'erp_code',
//            'width' => '20px',
//            'filter_index' => 'erp_code'
//        ));
        $this->addColumn('sales_rep_id', array(
            'header' => Mage::helper('epicor_comm')->__('Sales Rep Id'),
            'index' => 'sales_rep_id',
        ));
//        $this->addColumn('short_code', array(
//            'header' => Mage::helper('epicor_comm')->__('Short Code'),
//            'index' => 'short_code',
//            'width' => '20px',
//            'filter_index' => 'short_code'
//        ));
//        $this->addColumn('account_number', array(
//            'header' => Mage::helper('epicor_comm')->__('Account Number'),
//            'index' => 'account_number',
//            'width' => '20px',
//            'filter_index' => 'account_number'
//        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('epicor_comm')->__('Name'),
            'index' => 'name',
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $row->getId();
    }

    public function getGridUrl() {
        $data = $this->getRequest()->getParams();
        return $this->getUrl('*/*/*', array('grid' => true, 'field_id' => $data['field_id']));
    }
}