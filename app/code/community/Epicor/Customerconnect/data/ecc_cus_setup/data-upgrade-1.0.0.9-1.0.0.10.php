<?php

/**
 * Version 1.0.0.9 to 1.0.0.10 upgrade
 * 
 * insert of default data to service calls status mappings and RMA status mappings
 * 
 */ 

$installer = $this;
$installer->startSetup();

$installer->run("DELETE FROM " . $this->getTable('core/config_data') . " WHERE path LIKE '%CURS_request/grid_config%'");
$installer->run("DELETE FROM " . $this->getTable('core/config_data') . " WHERE path LIKE '%CUCS_request/grid_config%'");

$status = Mage::getModel('customerconnect/erp_mapping_rmastatus');

$status->setCode('Open');
$status->setStatus('Open');
$status->setState('Open');

$status->save();

$status = Mage::getModel('customerconnect/erp_mapping_rmastatus');

$status->setCode('Closed');
$status->setStatus('Closed');
$status->setState('Closed');

$status->save();

$status = Mage::getModel('customerconnect/erp_mapping_servicecallstatus');

$status->setCode('Open');
$status->setStatus('Open');
$status->setState('Open');

$status->save();

$status = Mage::getModel('customerconnect/erp_mapping_servicecallstatus');

$status->setCode('Closed');
$status->setStatus('Closed');
$status->setState('Closed');

$status->save();

$installer->endSetup();