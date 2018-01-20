<?php

/**
 * Upgrade - 1.0.5.3.1
 * 
 * Set default customer group
 */

$installer = $this;
$installer->startSetup();

//set default customer group to first one found

$config = Mage::getConfig()->init();
/* @var $config Mage_Core_Model_Config */

$default_group = Mage::getModel('customer/group')->getCollection()->setRealGroupsFilter()->getFirstItem()->getCustomerGroupId();
$config->saveConfig("epicor_comm_field_mapping/cus_mapping/customer_default_customer_group", $default_group);

Mage::app()->cleanCache(array('CONFIG'));


$installer->endSetup();



