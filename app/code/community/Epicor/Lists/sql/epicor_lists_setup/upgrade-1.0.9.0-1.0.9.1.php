<?php
/**
 * Version 1.0.7.8 to 1.0.7.9 upgrade
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
if (!$conn->tableColumnExists($installer->getTable('epicor_lists/list'), 'salesrep_erpaccount')) {
  
    $conn->addColumn($installer->getTable('epicor_lists/list'),'salesrep_erpaccount', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => true,
        'length'    => 10,
        'default'   => null,
        'comment'   => 'Salesrep Comparison Erpaccount'
        ));   
    
}  

$installer->endSetup();