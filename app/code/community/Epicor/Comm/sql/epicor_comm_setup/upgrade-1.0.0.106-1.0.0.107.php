<?php

/**
 * Upgrade 1.0.0.99 to 1.0.0.100
 * 
 * Adds last_msq_update to products
 * 
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();

$tables = array(
    'core/store_group',
);

foreach ($tables as $table) {
    $conn->addColumn($installer->getTable($table), 'brandimage', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'Brand Image'
    ));
    $conn->addColumn($installer->getTable($table), 'storeswitcher', array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'length' => 1,
        'comment' => 'Use in Store Switcher',
        'default' => 1,
    ));    
}

/***********************************************************************/
// apply default storeswitcher to existing stores in core/store_group
/***********************************************************************/

$stores = Mage::getModel('core/store_group')->getCollection();

foreach($stores as $store){
    $store->setStoreswitcher(true);
    $store->save();
}



