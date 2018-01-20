<?php

/**
 * Upgrade - 1.0.0.59 to 1.0.0.60
 * 
 * extending message customer address table to include email
 */

$installer = new Mage_Customer_Model_Entity_Setup();
//$installer = $this;
 
$installer->startSetup();
 
$installer->addAttribute('customer_address', 'email', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Email',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'visible_on_front' => 1
));
Mage::getSingleton('eav/config')
    ->getAttribute('customer_address', 'email')
    ->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'))
    ->save();
$installer->endSetup();