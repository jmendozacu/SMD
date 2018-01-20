<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$table = $installer->getTable('epicor_comm/erp_customer_group');


if (!$conn->tableColumnExists($table, 'cpn_editing')) {
    $conn->addColumn(
            $table, 'cpn_editing', array(
        'nullable' => true,
        'unsigned' => true,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'comment' => 'Customer Part Number/SKU editing enabling, NULL: Default, 0: Disabled, 1: Enabled',
        'default' => null
            )
    );
}
$installer->endSetup();
