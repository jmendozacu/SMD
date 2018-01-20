<?php

/**
 * Upgrade - 1.0.0.11 to 1.0.0.12
 * 
 * adding erp address codes to orders and quote addresses
 */
$installer = $this;
$installer->startSetup();
$conn=$installer->getConnection();

$conn->addColumn($this->getTable('sales_flat_order_address'), 
        'erp_address_code', 
        array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'comment'=> 'Erp address code'
        ));

$conn->addColumn($this->getTable('sales_flat_quote_address'), 
        'erp_address_code', 
        array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'comment'   => 'Erp address code'
        ));