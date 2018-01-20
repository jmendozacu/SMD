<?php

/**
 * Version 1.0.0.4 to 1.0.0.5 upgrade
 * 
 * Add next_delivery_date column to quote and order tables"
 */
$installer = $this;

$installer->startSetup();
$conn = $installer->getConnection();

$conn->addColumn($installer->getTable('sales/quote'), 'is_dda_date', array(
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'comment' => 'Is DDA Date'
    ));
$conn->addColumn($installer->getTable('sales/quote'), 'next_delivery_date', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DATE,
        'comment' => 'Next Delivery Date'
    ));


$installer->endSetup();



