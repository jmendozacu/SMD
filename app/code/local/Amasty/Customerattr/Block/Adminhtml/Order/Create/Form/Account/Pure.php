<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customerattr
 */


if (Mage::helper('core')->isModuleEnabled('Amasty_Orderattr')) {
    $autoloader = Varien_Autoload::instance();

    $autoloader->autoload('Amasty_Customerattr_Block_Adminhtml_Order_Create_Form_Account_Orderattr');
}
else {
    class Amasty_Customerattr_Block_Adminhtml_Order_Create_Form_Account_Pure extends Mage_Adminhtml_Block_Sales_Order_Create_Form_Account {}
}
