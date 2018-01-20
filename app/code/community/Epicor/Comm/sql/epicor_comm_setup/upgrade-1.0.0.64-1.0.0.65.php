<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

// Quote / Order Goods totals

$conn->addColumn(
    $installer->getTable('sales/shipment_track'), 
    'url', 
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'Tracking URL'
    )
);

$installer->endSetup();
