<?php

/**
 * Upgrade - 1.0.9.0.1-1.0.9.0.2
 * 
 * 
 */

/************************************************************************
Step : Upgrade script is required to ensure on upgrade that “Display Out of Stock Products" is set to Yes.
*************************************************************************/

$installer = $this;
$installer->startSetup();
$installer->deleteConfigData('cataloginventory/options/show_out_of_stock');
$setup = new Mage_Core_Model_Config();
$setup->saveConfig('cataloginventory/options/show_out_of_stock', '1', 'default', 0); 
Mage::app()->getCacheInstance()->cleanType('config');
$installer->endSetup();