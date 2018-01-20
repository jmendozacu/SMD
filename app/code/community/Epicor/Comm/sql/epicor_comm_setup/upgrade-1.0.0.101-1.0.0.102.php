<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */


/* * **********************************************************************
  Step : Create ERP Cuastomer Group hierarchy table
 * *********************************************************************** */

$conn->dropTable($this->getTable('epicor_comm/erp_customer_group_hierarchy'));
$table = $conn->newTable($this->getTable('epicor_comm/erp_customer_group_hierarchy'));

$table->addColumn(
        'id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'ID'
);

$table->addColumn(
        'parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => false,
    'unsigned' => true,
    'nullable' => false,
    'primary' => false,
        ), 'Parent ID'
);

$table->addColumn(
        'child_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => false,
    'unsigned' => true,
    'nullable' => false,
    'primary' => false,
        ), 'Child ID'
);

$table->addColumn(
        'type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
        ), 'Type'
);


$table->addColumn(
        'created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'default' => 0
        ), 'Created At'
);

$table->addColumn(
        'updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'default' => 0
        ), 'Updated At'
);

$table->addIndex(
        $installer->getIdxName(
                $this->getTable('epicor_comm/erp_customer_group_hierarchy'), array('parent_id')
        ), 'parent_id'
);

$table->addIndex(
        $installer->getIdxName(
                $this->getTable('epicor_comm/erp_customer_group_hierarchy'), array('child_id')
        ), 'child_id'
);

$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('epicor_comm/erp_customer_group'), 'entity_id', $this->getTable('epicor_comm/erp_customer_group_hierarchy'), 'parent_id'
        ), 'parent_id', $this->getTable('epicor_comm/erp_customer_group'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('epicor_comm/erp_customer_group'), 'entity_id', $this->getTable('epicor_comm/erp_customer_group_hierarchy'), 'child_id'
        ), 'child_id', $this->getTable('epicor_comm/erp_customer_group'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$conn->createTable($table);


//$erpAccounts = Mage::getModel('epicor_comm/customer_erpaccount')->getCollection();
//foreach ($erpAccounts as $erpAccount) {
//    /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
//    $defaults = $erpAccount->isDefaultForStore();
//    if ($erpAccount->isType('Customer')) {
//        $erpAccount->setAccountType('B2B');
//    }
//    $erpAccount->save();
//}

$installer->run("UPDATE " . $this->getTable('epicor_comm/customer_erpaccount') . " SET account_type = 'B2B' WHERE account_type = 'Customer' ");

$conn->addColumn($installer->getTable('epicor_comm/erp_customer_group'), 'account_type', array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 10,
    'default' => 'B2B',
    'comment' => 'ERP Account type'
));

$installer->endSetup();
