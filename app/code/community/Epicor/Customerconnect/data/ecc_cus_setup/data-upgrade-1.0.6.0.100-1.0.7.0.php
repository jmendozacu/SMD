<?php

/**
 * Version 1.0.0.9 to 1.0.0.10 upgrade
 * 
 * insert of default data to service calls status mappings and RMA status mappings
 * 
 */ 

$installer = $this;
$installer->startSetup();

$installer->run("DELETE FROM " . $this->getTable('core/config_data') . " WHERE path LIKE '%CCCS_request/grid_config%'");

$installer->endSetup();