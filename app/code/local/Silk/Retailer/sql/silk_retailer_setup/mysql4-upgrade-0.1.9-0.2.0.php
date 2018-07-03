<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/quote_item'), 'silk_delivery_date', array(
    'type' => Varien_Db_Ddl_Table::TYPE_DATE,
    'nullable'  => false,
    'comment' => 'Silk Delivery Date'
));
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'silk_delivery_date', array(
    'type' => Varien_Db_Ddl_Table::TYPE_DATE,
    'nullable'  => false,
    'comment' => 'Silk Delivery Date'
));

$installer->endSetup();