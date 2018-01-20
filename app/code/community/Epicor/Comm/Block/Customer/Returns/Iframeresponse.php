<?php

/**
 * Return iframe post response
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Iframeresponse extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('epicor_comm/customer/returns/iframe_response.phtml');
    }

    public function getJson()
    {
        return Mage::registry('response_json'); 
    }

}
