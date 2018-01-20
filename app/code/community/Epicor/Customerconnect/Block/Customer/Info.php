<?php
/**
 * @method string getTitle()
 * @method void setTitle(string $title)
 * @method int getColumnCount()
 * @method void setColumnCount(int $count)
 * @method bool getOnRight()
 * @method void setOnRight(bool $bool)
 */
class Epicor_Customerconnect_Block_Customer_Info extends Mage_Core_Block_Template {
    /**
     *  @var Varien_Object 
     */
    protected $_infoData = array();
    
    public function _construct() {
        parent::_construct();
        $this->setTemplate('customerconnect/info.phtml');
        $this->setColumnCount(3);
    }
    
    /**
     * 
     * @return Epicor_Customerconnect_Helper_Data
     */
    public function getHelper($type = null)
    {
        return Mage::helper('customerconnect');
    }
}