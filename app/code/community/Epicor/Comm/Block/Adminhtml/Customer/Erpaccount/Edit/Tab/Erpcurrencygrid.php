<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Erpcurrencygrid extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface {
    protected $_erpCustomer;
    public function __construct()
    {
        parent::__construct();      
        $this->setId('currencyGrid');
        $this->setSaveParametersInSession(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);       
    }
    
  
    public function canShowTab() {
        return true;
    }

    public function getTabLabel() {
        return 'Currency Grid';
    }

    public function getTabTitle() {
        return 'Currency Grid';
    }

    public function isHidden() {
        return false;
    }


    protected function _prepareCollection() {      
        $collection = new Varien_Data_Collection();
    
        $allowedCurrencies = new Varien_Object(Mage::registry('customer_erp_account')->getAllCurrencyData());
        foreach($allowedCurrencies->getData() as $currency){
            $data = new Varien_Object($currency->getData());
            $collection->addItem($data); 
        }
        
        $this->setCollection($collection);        
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('Currency Code', array(
            'header' => Mage::helper('epicor_comm')->__('Currency Code'),
            'width' => '50',
            'index' => 'currency_code',
            'sortable'  => false,
        )); 
          
        $this->addColumn('credit_limit', array(
            'header' => Mage::helper('epicor_comm')->__('Credit Limit'),
            'width' => '50',
            'index' => 'credit_limit',
            'sortable'  => false,
        ));
        
        $this->addColumn('balance', array(
            'header' => Mage::helper('epicor_comm')->__('Current Balance'),
            'width' => '50',
            'index' => 'balance',
            'sortable'  => false,
        ));
        $this->addColumn('onstop', array(
            'header' => Mage::helper('epicor_comm')->__('On Stop'),
            'width' => '50',
            'index' => 'onstop',
            'sortable'  => false,
        ));
        
        $this->addColumn('min_order_amount', array(
            'header' => Mage::helper('epicor_comm')->__('Min Order Amount'),
            'width' => '50',
            'index' => 'min_order_amount',
            'sortable'  => false
        ));
        
        return parent::_prepareColumns();
    }

}