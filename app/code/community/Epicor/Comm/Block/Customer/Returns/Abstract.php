<?php

/**
 * Returns creation page, Abstract block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Abstract extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();
    }

    public function getEncodedReturn()
    {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */
        if (Mage::registry('return_id')) {
            $return = $helper->encodeReturn(Mage::registry('return_id'));
        } else {
            $return = '';
        }

        return $return;
    }

    public function getEncodedLines()
    {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */
        if (Mage::registry('return_lines')) {
            $lines = $helper->encodeReturn(Mage::registry('return_lines'));
        } else {
            $lines = '';
        }

        return $lines;
    }

    public function getEncodedLineAttachments()
    {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */
        if (Mage::registry('return_line_attachments')) {
            $data = $helper->encodeReturn(Mage::registry('return_line_attachments'));
        } else {
            $data = '';
        }

        return $data;
    }

    public function getEncodedAttachments()
    {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */
        if (Mage::registry('return_attachments')) {
            $data = $helper->encodeReturn(Mage::registry('return_attachments'));
        } else {
            $data = '';
        }

        return $data;
    }

    /**
     * 
     * @return Epicor_Comm_Model_Customer_Return
     */
    public function getReturn()
    {
        return Mage::registry('return_model');
    }

    /**
     * 
     * @return array
     */
    public function getLines()
    {
        return Mage::registry('return_lines');
    }

    /**
     * 
     * @return array
     */
    public function getLineAttachments()
    {
        return Mage::registry('return_line_attachments');
    }

    /**
     * 
     * @return Epicor_Comm_Model_Customer_Return
     */
    public function getAttachments()
    {
        return Mage::registry('return_attachments');
    }

    /**
     * 
     * @return Epicor_Comm_Model_Customer_Return
     */
    public function returnActionAllowed($action)
    {
        $hasAction = false;
        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */
        if ($return) {
            $hasAction = $return->isActionAllowed($action);
        }

        return $hasAction;
    }

    /**
     * Checks a return config flag
     * 
     * @return boolean
     */
    public function checkConfigFlag($path, $type = null)
    {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        return $helper->checkConfigFlag($path, $type);
    }

    /**
     * Checks a return config value is present
     * 
     * @return boolean
     */
    public function configHasValue($path, $value, $type = null)
    {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        return $helper->configHasValue($path, $value, $type);
    }

    
    /**
     * get return type flag
     * 
     * @return boolean
     */
    public function getReturnType()
    {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        return $helper->getReturnUserType();
    }
    
    public function getReturnBarHtml() 
    {
        return Mage::app()->getLayout()->createBlock('epicor_comm/customer_returns_returnbar')->toHtml();
    }
    
    public function returnExists()
    {

        if (Mage::registry('return_model')) {
            $return = Mage::registry('return_model');
            /* @var $return Epicor_Comm_Model_Customer_Return */
            
            $exists = $return->getId() ? true : false;
        } else {
            $exists = false;
        }
        
        return $exists;
    }
}
