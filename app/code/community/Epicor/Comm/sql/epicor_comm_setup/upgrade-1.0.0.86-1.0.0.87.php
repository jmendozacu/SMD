<?php
$installer = $this;
$installer->startSetup();
$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$table = $installer->getTable('epicor_comm/customer_return');
if (!$conn->tableColumnExists($table, 'customer_id')) {
    $conn->addColumn(
        $table, 
        'customer_id',
        array(
            'nullable' => true,
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'comment' => 'Customer ID',
        )
    );
} 
if (!$conn->tableColumnExists($table, 'erp_account_id')) {
    $conn->addColumn(
        $table, 
        'erp_account_id',
        array(
            'nullable' => true,
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'comment' => 'ERP Account ID',
        )
    );
}    
if (!$conn->tableColumnExists($table, 'is_global')) {
    $conn->addColumn(
        $table, 
        'is_global',
        array(
            'nullable' => true,
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'comment' => 'Is Return global to ERP Account',
            'default' => 0,
        )
    );
}    


$conn->addIndex(
        $table,
        $installer->getIdxName(
            $table,
            array('customer_id')
        ),
        'customer_id');

$conn->addIndex(
        $table,
        $installer->getIdxName(
            $table, 
            array('erp_account_id')
        ),
        'erp_account_id'
);

$conn->addForeignKey(
    $installer->getFkName(
        $this->getTable('epicor_comm/erp_customer_group'), 
        'entity_id', 
        $table,
        'erp_account_id'
    ), 
    $table, 
    'erp_account_id', 
    $this->getTable('epicor_comm/erp_customer_group'),
    'entity_id', 
    Varien_Db_Ddl_Table::ACTION_CASCADE, 
    Varien_Db_Ddl_Table::ACTION_CASCADE
);


$installer->endSetup();