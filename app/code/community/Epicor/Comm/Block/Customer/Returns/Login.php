<?php

/**
 * Returns creation page, Login block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Login extends Epicor_Comm_Block_Customer_Returns_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('Login'));
        $this->setTemplate('epicor_comm/customer/returns/login.phtml');
        //
    }
    
    public function getMessages()
    {
        return Mage::getSingleton('customer/session')->getMessages(true);
    }

    public function getPostAction()
    {
        return Mage::getUrl('customer/account/loginPost', array('_secure'=>true));
    }
}
