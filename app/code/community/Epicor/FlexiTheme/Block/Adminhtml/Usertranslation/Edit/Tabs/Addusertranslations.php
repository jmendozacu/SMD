<?php

/**
 * Quick add block
 * 
 * Displays the quick add to Basket / wishlist block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Flexitheme_Block_Adminhtml_Usertranslation_Edit_Tabs_Addusertranslations extends Mage_Core_Block_Template {
    protected $_formkey;
    public function __construct()
    {
        $this->setTemplate('flexitheme/usertranslation/addusertranslation.phtml');
        $this->_formkey = Mage::getSingleton('core/session')->getFormKey();
        parent::__construct();
    }
}   