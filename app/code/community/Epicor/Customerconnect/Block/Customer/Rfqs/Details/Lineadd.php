<?php

/**
 * RFQ Line quick add block
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lineadd extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('customerconnect/customer/account/rfqs/details/line_add.phtml');
        $this->setTitle($this->__('Add Line'));
    }

    /**
     * Checks to see if the autocomplete is allowed
     */
    public function autocompleteAllowed()
    {
        return Mage::getStoreConfigFlag('quickadd/autocomplete_enabled');
    }
    
    /**
     * Checks to see if the autocomplete is allowed
     */
    public function customAllowed()
    {
        return Mage::getStoreConfigFlag('customerconnect/crq_options/quickadd_custom_allowed');
    }

}
