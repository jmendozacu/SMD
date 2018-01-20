<?php

/**
 * Upgrade - 1.0.0.3 to 1.0.0.4
 * 
 * adding access element excluded from portal flag
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
try{  
    $conn->addColumn($installer->getTable('epicor_common/access_element'), 'portal_excluded', array(
        'identity' => false,
        'nullable' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'primary' => false,
        'default' => 0,
        'comment' => 'Excluded From B2b Portal'
    ));
} catch (Exception $ex) {
   Mage::log('epicr b2b sql upgrade 1.0.0.3-1.0.0.4 failed: ');
   Mage::log($ex);
}

$installer->endSetup();
