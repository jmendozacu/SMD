<?php

/**
 * Upgrade - 1.0.0.29 to 1.0.0.30
 * 
 * Renaming address_type attribute on customer addresses as it's causing issues on guest checkout
 */
$installer = $this;
$installer->startSetup();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */


/* * **********************************************************************
  Step : Add Attributes to Customer Addresses
 * *********************************************************************** */
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
try{
	$setup->updateAttribute('customer_address', 'address_type', array('attribute_code' => 'erp_address_type', 'frontend_label' => 'ERP Address Type'));
} catch(Exception $ex){
	Mage::log('epicor comm upgrade-1.0.0.29-10.0.30 Script failure: ');
        Mage::log($ex);
}
$installer->endSetup();