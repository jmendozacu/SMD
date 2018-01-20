<?php

/**
 * @method string getTitle()
 * @method void setTitle(string $title)
 * @method int getColumnCount()
 * @method void setColumnCount(int $count)
 * @method bool getOnRight()
 * @method void setOnRight(bool $bool)
 * @method bool getOnLeft()
 * @method void setOnLeft(bool $bool)
 */
class Epicor_Supplierconnect_Block_Customer_Info extends Mage_Core_Block_Template
{

    /**
     *  @var Varien_Object 
     */
    protected $_infoData = array();
    protected $_extraData = array();

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('supplierconnect/customer/account/info.phtml');
        $this->setColumnCount(3);
    }

    /**
     * 
     * @return Epicor_Supplierconnect_Helper_Data
     */
    public function getHelper($type = null)
    {
        return Mage::helper('supplierconnect');
    }

}
