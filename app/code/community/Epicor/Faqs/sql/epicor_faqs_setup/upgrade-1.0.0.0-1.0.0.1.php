<?php
/**
 * @package Mage_Core
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$connection->addColumn($installer->getTable('epicor_faqs/faqs'), 'keywords', array(
    'nullable' => true,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'Keywords',
    'default' => null
));
$installer->endSetup();