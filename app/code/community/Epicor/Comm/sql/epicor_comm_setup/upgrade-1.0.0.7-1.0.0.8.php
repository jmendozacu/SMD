<?php

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->removeAttribute('catalog_category', 'erp_code');

$installer->addAttribute('catalog_category', 'erp_code', array(
    'group' => 'General Information',
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 50,
    'input' => 'text',
    'label' => 'ERP Code',
    'class' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => true,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false
));

$installer->endSetup();

