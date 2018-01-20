<?php

$installer = $this;
$installer->startSetup();
$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('sales/order'), 
        'customer_order_ref',
        array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment'   => 'Customer Order Ref',
        'length'    => 255
        ));
$conn->addColumn($installer->getTable('sales/quote'), 
        'customer_order_ref',
        array(
        'unsigned'  => true,
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment'   => 'Customer Order Ref',
        'length'    => 255
        ));

$installer->endSetup();
