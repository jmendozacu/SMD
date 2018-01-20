<?php

/**
 * RFQ Lines container block
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Linescontainer extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('customerconnect/customer/account/rfqs/details/lines.phtml');
    }

    /**
     * Checks to see if the autocomplete is allowed
     */
    public function autocompleteAllowed()
    {
        return Mage::getStoreConfigFlag('quickadd/autocomplete_enabled');
    }

}
