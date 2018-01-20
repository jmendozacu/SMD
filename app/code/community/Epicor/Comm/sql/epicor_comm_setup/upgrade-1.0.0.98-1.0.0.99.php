<?php

/**
 * Version 1.0.0.98 to 1.0.0.99 upgrade
 * 
 * removing CRRS grid config to restore default
 * 
 */ 

$installer = $this;
$installer->startSetup();

$installer->run("DELETE FROM " . $this->getTable('core/config_data') . " WHERE path LIKE '%CRRS_request/grid_config%'");

$installer->endSetup();