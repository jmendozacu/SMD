<?php

/**
 * Upgrade - 1.0.0.10 to 1.0.0.11
 * 
 * extending message log table xml columns
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

if ($conn->tableColumnExists($this->getTable('epicor_salesrep/pricing_rule'), 'action_amount')) {
    $conn->modifyColumn($this->getTable('epicor_salesrep/pricing_rule'), 'action_amount', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'comment' => 'Sale Rep Margin'
    ));
}

$installer->endSetup();
