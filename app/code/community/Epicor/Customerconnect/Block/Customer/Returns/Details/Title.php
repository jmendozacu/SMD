<?php

/**
 * Return Details page title
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Returns_Details_Title extends Epicor_Customerconnect_Block_Customer_Title
{

    protected $_rereturnType = 'Returns';

    public function _construct()
    {
        parent::_construct();
        $this->_setTitle();
    }

    /**
     * Sets the page title
     */
    protected function _setTitle()
    {
        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */
        $this->_title = $this->__('Return : %s', $return->getCustomerReference() ? : $return->getErpReturnsNumber());
    }

    /**
     * Returns whether an entity can be reordered or not
     * 
     * @return boolean
     */
    public function canReorder()
    {
        return false;
    }

    /**
     * Returns whether an entity can be returned or not
     * 
     * @return boolean
     */
    public function canReturn()
    {
        return false;
    }

    /**
     * Returns whether an entity can be edited or not
     * 
     * @return boolean
     */
    public function canEdit()
    {
        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */
        $actions = $return->getActions();
        
        return !empty($actions);
    }

    /**
     * Returns whether an entity can be deleted or not
     * 
     * @return boolean
     */
    public function canDelete()
    {
        // use actions here to determine return can be deleted
        return true;
    }

    /**
     * Returns whether an entity can be deleted or not
     * 
     * @return boolean
     */
    public function getEditUrl()
    {
        // edit link may need to be different if it exists in our system or not
        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */

        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        if (!$return->isObjectNew()) {
            $params = array(
                'return' => $helper->encodeReturn($return->getId())
            );
            $url = Mage::getUrl('epicor_comm/returns/index', $params);
        } else {
            $params = array(
                'erpreturn' => $helper->encodeReturn($return->getErpReturnsNumber())
            );
            $url = Mage::getUrl('epicor_comm/returns/index', $params);
        }

        return $url;
    }

    /**
     * Returns whether an entity can be deleted or not
     * 
     * @return boolean
     */
    public function getDeleteUrl()
    {
        // edit link may need to be different if it exists in our system or not
        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */

        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        if (!$return->isObjectNew()) {
            $params = array(
                'return' => $helper->encodeReturn($return->getId())
            );
            $url = Mage::getUrl('epicor_comm/returns/delete', $params);
        } else {
            $params = array(
                'erpreturn' => $helper->encodeReturn($return->getErpReturnsNumber())
            );
            $url = Mage::getUrl('epicor_comm/returns/delete', $params);
        }

        return $url;
    }

}
