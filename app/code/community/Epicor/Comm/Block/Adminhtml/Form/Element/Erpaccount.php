<?php

class Epicor_Comm_Block_Adminhtml_Form_Element_Erpaccount extends Epicor_Common_Block_Adminhtml_Form_Element_Erpaccounttype 
{

    public function __construct($attributes = array())
    {
        $this->_restrictToType = 'customer';
        parent::__construct($attributes);
    }
}
