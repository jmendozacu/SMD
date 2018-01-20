<?php
/**
 * Version 1.0.7.10 to 1.0.7.11 upgrade
 * 
 */
$installHelper = Mage::helper('epicor_common/setup');
/* @var $installHelper Epicor_Common_Helper_Setup */

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

/**
 * LISTS
 */

// Create Lists Table
if (!$conn->tableColumnExists($installer->getTable('epicor_lists/list'), 'owner_id')) {
  
    $conn->addColumn($installer->getTable('epicor_lists/list'),'owner_id', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => true,
        'comment'   => 'Erp Owner Id'
        ));   
    
}    

if ($conn->tableColumnExists($installer->getTable('epicor_lists/list'), 'source')) {
    $conn->changeColumn($installer->getTable('epicor_lists/list'), 'source', 'source', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 30,
        'default' => 'web',
        'nullable' => false,
        'comment' =>'Source (Customer / Web / ERP)'
    ));
}


$installer->endSetup();