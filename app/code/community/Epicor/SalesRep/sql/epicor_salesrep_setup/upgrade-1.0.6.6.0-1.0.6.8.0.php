<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$tableName = $installer->getTable('epicor_salesrep/pricing_rule_product');

if (!$conn->tableColumnExists($tableName, 'store_id')) {
    $conn->addColumn($tableName, 'store_id', array(
        'nullable' => true,
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'Store Id'
    ));
}

$idxTypeUnique = Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE;
$fields = array('pricing_rule_id', 'product_id');
$dropIdxName = $installer->getIdxName($tableName, $fields, $idxTypeUnique);
$conn->dropIndex($tableName, $dropIdxName);

$fields[] = 'store_id';
$idxName = $installer->getIdxName($tableName, $fields, $idxTypeUnique);
$conn->addIndex($tableName, $idxName, $fields, $idxTypeUnique);

$installer->endSetup();
