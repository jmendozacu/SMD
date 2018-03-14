<?php

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->addAttribute('customer', 'ecc_cuco_pending', array(
    'group' => 'General',
    'label' => 'Is ECC CUCO Pending?',
    'type' => 'int',
    'input' => 'boolean',
    'default' => 0,
    'visible' => true,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
));

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'ecc_cuco_pending');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();

$installer->endSetup();
