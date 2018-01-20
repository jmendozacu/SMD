<?php

/**
 * Upgrade - 1.0.0.15 to 1.0.0.16
 * 
 * WSO-1300 Add store_id to mapping tables
 */


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$tables_to_add_store_id = array(
    'customerconnect/erp_mapping_erporderstatus',
    'customerconnect/erp_mapping_erpquotestatus',
    'customerconnect/erp_mapping_invoicestatus',
    'customerconnect/erp_mapping_rmastatus',
    'customerconnect/erp_mapping_servicecallstatus'

);

foreach($tables_to_add_store_id as $table){
    $conn->addColumn(
        $installer->getTable($table),
        'store_id',
        array(
            'nullable' => false,
            'primary' => false,
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'default' => 0,
            'comment' => 'Store id value'
        )
    );
}


$installer->endSetup();
