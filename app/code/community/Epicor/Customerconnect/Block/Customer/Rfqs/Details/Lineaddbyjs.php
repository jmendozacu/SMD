<?php

/**
 * RFQ Details page redirector
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lineaddbyjs extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('customerconnect/customer/account/rfqs/details/line_add_by_js.phtml');
    }

    public function getJson()
    {
        return Mage::registry('line_add_json'); 
    }

}
