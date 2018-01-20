<?php

/**
 * Upgrade - 1.0.6.4.1.1.0.6.4.2
 *
 * Adding location name and gqr line number to quote lines and order lines
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

// ECC Location Name

if (!$conn->tableColumnExists($installer->getTable('sales/quote_item'), 'ecc_msq_base_price')) {
    $conn->addColumn($installer->getTable('sales/quote_item'), 'ecc_msq_base_price', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'comment' => 'ECC MSQ Base Price'
    ));
}

$installer->endSetup();