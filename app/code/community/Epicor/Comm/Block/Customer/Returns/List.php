<?php

/**
 * Returns creation page, Notes block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_List extends Epicor_Comm_Block_Customer_Returns_Abstract
{

    protected $_collection = array();

    public function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('Returns List'));
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $this->_collection = Mage::getModel('epicor_comm/customer_return')->getCollection();
        /* @var $collection Epicor_Comm_Model_Mysql4_Customer_Return_Collection */
        $this->_collection->filterByCustomer($customer);
    }

    public function getReturns()
    {
        return $this->_collection;
    }

    public function getViewUrl($return)
    {
        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */

        $quoteDetails = array(
            'id' => $return->getId()
        );

        $requested = $helper->urlEncode($helper->encrypt(serialize($quoteDetails)));

        return Mage::getUrl('epicor_comm/returns/view', array('return' => $requested));
    }

}
