<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$helper = Mage::helper('flexitheme/setup');
/* @var $helper Epicor_Flexitheme_Helper_Setup */

/* * **********************************************************************
  Step : Populate Layout Blocks
 * *********************************************************************** */
try{
    $helper->createLayoutBlock('Language Switcher', 'page/switch', 'page/switch/languages.phtml', NULL, NULL, NULL, NULL, 'store_language', NULL);
} catch (Exception $ex) {
    mage::log('Language Switcher block already existed, no need to create');
}
