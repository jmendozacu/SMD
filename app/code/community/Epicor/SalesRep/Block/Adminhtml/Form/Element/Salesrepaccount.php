<?php

class Epicor_SalesRep_Block_Adminhtml_Form_Element_Salesrepaccount extends Epicor_Common_Block_Adminhtml_Form_Element_Erpaccounttype 
{

    public function __construct($attributes = array())
    {
        $this->_restrictToType = 'salesrep';
        parent::__construct($attributes);
    }
}
