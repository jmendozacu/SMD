<?php

/**
 * Returns creation page, Attachments block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Attachments extends Epicor_Comm_Block_Customer_Returns_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('Attachments'));
        $this->setTemplate('epicor_comm/customer/returns/attachments.phtml');
    }

    public function getAttachmentsHtml()
    {
        return Mage::app()->getLayout()->createBlock('epicor_comm/customer_returns_attachment_lines')->toHtml();
    }

}
