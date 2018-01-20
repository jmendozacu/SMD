<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

/************************************************************************
Step : Add column to epicor_comm_erp_mapping_payment 
*************************************************************************/
$table = $installer->getTable('epicor_comm/erp_mapping_payment');
$conn->addColumn($table, 
        'gor_trigger', 
        array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length' => 90,
                'comment' => 'Gor Trigger'
        ));

