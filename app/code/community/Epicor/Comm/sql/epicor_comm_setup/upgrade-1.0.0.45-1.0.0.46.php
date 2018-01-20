<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

/************************************************************************
Step : Add column prevent_repricing to epicor_comm_erp_mapping_payment 
*************************************************************************/
$table = $installer->getTable('epicor_comm/erp_mapping_payment');
$conn->addColumn($table, 
        'gor_online_prevent_repricing', 
        array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length' => 20,
                'comment' => 'Prevent Repricing for GOR for this Payment Method when order placed online?'
        ));
$conn->addColumn($table, 
        'gor_offline_prevent_repricing', 
        array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length' => 20,
                'comment' => 'Prevent Repricing for GOR for this Payment Method when order placed offline?'
        ));
$conn->addColumn($table, 
        'bsv_online_prevent_repricing', 
        array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length' => 20,
                'comment' => 'Prevent Repricing for BSV for this Payment Method when order placed online?'
        ));
$conn->addColumn($table, 
        'bsv_offline_prevent_repricing', 
        array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length' => 20,
                'comment' => 'Prevent Repricing for BSV for this Payment Method when order placed offline?'
        ));

$installer->endSetup();

