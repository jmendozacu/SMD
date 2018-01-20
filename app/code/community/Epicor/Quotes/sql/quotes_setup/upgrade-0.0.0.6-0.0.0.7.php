<?php

$installer = $this;
$installer->startSetup();
$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('sales/order'), 
        'epicor_quote_id',
        array(
        'unsigned'  => true,
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'   => 'Epicor Quote Id'
        ));
$conn->addColumn($installer->getTable('sales/quote'), 
        'epicor_quote_id',
        array(
        'unsigned'  => true,
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'   => 'Epicor Quote Id'
        ));

$installer->endSetup();