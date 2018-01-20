<?php

/**
 * Upgrade - 1.0.0.9 to 1.0.0.10
 *
 * Adding Epicor File table
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->dropTable($this->getTable('epicor_common/file'));
$table = $conn->newTable($this->getTable('epicor_common/file'));

$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
    array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
    ), 'ID'
);

$table->addColumn(
    'erp_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255,
    array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    ), 'Erp ID'
);

$table->addColumn(
    'filename', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255,
    array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    ), 'Filename'
);

$table->addColumn(
    'description', Varien_Db_Ddl_Table::TYPE_VARCHAR, '', array(
    'nullable' => true,
    ), 'Description'
);
$table->addColumn(
    'url', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'Url'
);

$table->addColumn(
    'customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'nullable' => true,
    'unsigned' => true,
    ), 'Customer ID'
);

$table->addColumn(
    'erp_account_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
    array(
    'nullable' => true,
    'unsigned' => true,
    ), 'ERP Account ID'
);

$table->addColumn(
    'source', Varien_Db_Ddl_Table::TYPE_VARCHAR, 5, array(
    'nullable' => true,
    ), 'File Source'
);

$table->addColumn(
    'created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'nullable' => false,
    'default' => 0
    ), 'Created At'
);

$table->addColumn(
    'updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'nullable' => false,
    'default' => 0
    ), 'Updated At'
);


$table->addColumn(
    'action', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1, array(
    'nullable' => false,
    'default' => ''
    ), 'Action'
);

$table->addColumn(
    'previous_data', Varien_Db_Ddl_Table::TYPE_TEXT, '4G', array(
    'nullable' => false,
    'default' => ''
    ), 'Previous Data'
);

$conn->createTable($table);

$conn->addForeignKey(
    $installer->getFkName('customer/entity', 'entity_id', 'epicor_common/file', 'customer_id'),
    $installer->getTable('epicor_common/file'), 'customer_id', $installer->getTable('customer/entity'), 'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$conn->addForeignKey(
    $installer->getFkName('epicor_comm/erp_customer_group', 'entity_id', 'epicor_common/file', 'erp_account_id'),
    $installer->getTable('epicor_common/file'), 'erp_account_id',
    $installer->getTable('epicor_comm/erp_customer_group'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);


$installer->endSetup();
