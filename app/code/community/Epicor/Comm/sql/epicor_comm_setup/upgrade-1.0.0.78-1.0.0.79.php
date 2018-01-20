<?php

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->run("
    UPDATE  " . $this->getTable('epicor_comm/message_log') . "
    SET     message_secondary_subject = REPLACE(message_secondary_subject, 'Quote ID', 'Basket Quote ID')
    WHERE   message_type = 'GOR'
");

$installer->addAttribute(
    'catalog_product', 'stk_type',
    array(
    'group' => 'General',
    'label' => 'STK Type flag',
    'type' => 'varchar',
    'input' => 'text',
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'used_in_product_listing' => true
    )
);

$installer->endSetup();
