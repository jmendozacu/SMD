<?php

/**
 * Returns creation page, Review block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Review extends Epicor_Comm_Block_Customer_Returns_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('Review'));
        $this->setTemplate('epicor_comm/customer/returns/review.phtml');
        Mage::register('review_display', 1);
    }

    public function getLinesHtml()
    {
        return Mage::app()->getLayout()->createBlock('epicor_comm/customer_returns_lines')->toHtml();
    }

    public function getAttachmentsHtml()
    {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        $html = '';
        if ($helper->checkConfigFlag('return_attachments')) {
            $html = Mage::app()->getLayout()->createBlock('epicor_comm/customer_returns_attachment_lines')->toHtml();
        }
        
        return $html;
    }

    /**
     * 
     * @return Epicor_Comm_Model_Customer_Return
     */
    public function getSuccess()
    {
        return Mage::registry('return_success');
    }
    
}
