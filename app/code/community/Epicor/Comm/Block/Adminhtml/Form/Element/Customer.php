<?php

class Epicor_Comm_Block_Adminhtml_Form_Element_Customer extends Epicor_Common_Block_Adminhtml_Form_Element_Erpaccounttype 
{
    
    
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        
        $this->_restrictToType = 'mage_customer';
        
        $this->_accountType = 'mage_customer';
        
        $this->_defaultLabel = 'No Customer Selected';
        
        $this->_types = array(
            'mage_customer' => array(
                'label' => 'Customer',
                'field' => 'id',
                'model' => 'customer/customer',
                'url' => 'adminhtml/epicorcomm_customer/listcustomers/',
                'priority' => 10
            )
        );
    }
}
