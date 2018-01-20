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
if (!$conn->tableColumnExists($installer->getTable('epicor_lists/list'), 'erp_accounts_exclusion')) {
  
    $conn->addColumn($installer->getTable('epicor_lists/list'),'erp_accounts_exclusion', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => false,
        'length'    => 1,
        'default'   => 'N',
        'comment'   => 'Erp Accounts Exclusion'
        ));   
    
}    

$lists = Mage::getSingleton('core/resource')->getTableName('epicor_lists/list');
$lists_erp_account = Mage::getSingleton('core/resource')->getTableName('epicor_lists/list_erp_account');
$sql = "UPDATE {$lists} 
        SET erp_accounts_exclusion = 'Y'      
        WHERE
        erp_account_link_type IN ('B' , 'C', 'E')
           AND type != 'Co'
           AND ID NOT IN (SELECT DISTINCT
               (list_id)
           FROM
               {$lists_erp_account}) 
            ";
$installer->run($sql);

$installer->endSetup();