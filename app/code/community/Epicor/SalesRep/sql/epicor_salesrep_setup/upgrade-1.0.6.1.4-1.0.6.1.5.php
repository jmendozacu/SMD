<?php

/**
 * Upgrade - 1.0.6.1.4 to 1.0.6.1.5
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

if ($conn->tableColumnExists($this->getTable('epicor_salesrep/pricing_rule'), 'action_amount')) {
    $conn->modifyColumn($this->getTable('epicor_salesrep/pricing_rule'), 'action_amount', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'comment' => 'Sale Rep Margin',
        'length' => '16,4',
    ));
}

$tableName = $this->getTable('epicor_salesrep/pricing_rule_product');

if ($conn->tableColumnExists($tableName, 'sales_rep_account_id')) {
    $conn->dropColumn($tableName, 'sales_rep_account_id');
}

if ($conn->tableColumnExists($tableName, 'action_operator')) {
    $conn->dropColumn($tableName, 'action_operator');
}

if ($conn->tableColumnExists($tableName, 'action_amount')) {
    $conn->dropColumn($tableName, 'action_amount');
}

if ($conn->tableColumnExists($tableName, 'lowest_price_allowed')) {
    $conn->dropColumn($tableName, 'lowest_price_allowed');
}

$idxName = $installer->getIdxName(
        $tableName, array('pricing_rule_id', 'product_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$conn->addIndex($tableName, $idxName, array('pricing_rule_id', 'product_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE);

$installer->endSetup();
