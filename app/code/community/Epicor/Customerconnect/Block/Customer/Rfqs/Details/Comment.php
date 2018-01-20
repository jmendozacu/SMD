<?php

/**
 * RFQ comment block
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Comment extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('customerconnect/customer/account/rfqs/details/comment.phtml');
    }

    public function getComment()
    {
        $rfq = Mage::registry('customer_connect_rfq_details');
        return $this->htmlEscape($rfq->getNoteText());
    }

}
