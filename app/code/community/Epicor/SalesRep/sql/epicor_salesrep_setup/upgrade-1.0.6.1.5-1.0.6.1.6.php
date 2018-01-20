<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$tableName = $this->getTable('epicor_salesrep/pricing_rule');
if ($conn->tableColumnExists($tableName, 'to_date')) {
    $conn->modifyColumn($tableName, 'to_date', array(
        'nullable' => true,
        'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
        'comment' => 'Finish On Date'
    ));
} else {
    $conn->addColumn($tableName, 'to_date', array(
        'nullable' => true,
        'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
        'comment' => 'Finish On Date'
    ));
}


if ($conn->tableColumnExists($tableName, 'from_date')) {
    $conn->modifyColumn($tableName, 'from_date', array(
        'nullable' => true,
        'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
        'comment' => 'Start From Date'
    ));
} else {
    $conn->addColumn($this->getTable('epicor_salesrep/account'), 'from_date', array(
        'nullable' => true,
        'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
        'comment' => 'Start From Date'
    ));
}
$installer->endSetup();
