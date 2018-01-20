<?php

class Epicor_Quotes_Block_Adminhtml_Quotes_Edit_Customerinfo extends Epicor_Quotes_Block_Adminhtml_Quotes_Edit_Abstract
{
    /**
     * 
     * @return Varien_Object
     */
    public function getCustomerGroup()
    {
        return $this->getQuote()->getCustomerGroup(true);
    }

}
