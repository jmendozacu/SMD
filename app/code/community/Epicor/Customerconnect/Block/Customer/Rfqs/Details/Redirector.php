<?php

/**
 * RFQ Details page redirector
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Redirector extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('customerconnect/customer/account/rfqs/details/redirector.phtml');
    }

    public function getRedirectUrl()
    {
        return Mage::registry('rfq_redirect_url');
    }

}
