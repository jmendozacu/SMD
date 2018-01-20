<?php

/**
 * Returns creation page, Return block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Return extends Epicor_Comm_Block_Customer_Returns_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('Return'));
        $this->setTemplate('epicor_comm/customer/returns/return.phtml');
    }

    public function getGuestEmail()
    {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */
        if (Mage::registry('guest_email')) {
            $email = $helper->encodeReturn(Mage::registry('guest_email'));
        } else {
            $email = '';
        }

        return $email;
    }

    public function getGuestName()
    {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */
        if (Mage::registry('guest_name')) {
            $name = $helper->encodeReturn(Mage::registry('guest_name'));
        } else {
            $name = '';
        }

        return $name;
    }

    public function getFindByOptions()
    {
        $options = array();

        $return = $this->configHasValue('find_by', 'return_number');
        $case = $this->configHasValue('find_by', 'case_number');
        $ref = $this->configHasValue('find_by', 'customer_ref');

        if ($return) {
            $options[] = array(
                'value' => 'return',
                'label' => $this->__('Return Number'),
            );
        }

        if ($case) {
            $options[] = array(
                'value' => 'case_no',
                'label' => $this->__('Case Management Number'),
            );
        }

        if ($ref) {
            $options[] = array(
                'value' => 'customer_ref',
                'label' => $this->__('Customer Reference'),
            );
        }

        return $options;
    }
    
}
