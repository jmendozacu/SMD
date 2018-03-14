<?php

/**
 * Upgrade - 1.0.8.0.2 - 1.0.8.03 
 * Repeat of 1.0.8.0.2, as previous version did not have updated code    
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$installer->run("UPDATE " . $installer->getTable('eav_attribute') . " SET created_by = 'N' WHERE created_by is null or created_by != 'STK'");

$installer->endSetup();
