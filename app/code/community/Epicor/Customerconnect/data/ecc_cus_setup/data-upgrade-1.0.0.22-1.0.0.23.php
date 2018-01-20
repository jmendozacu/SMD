<?php

/**
 * Version 1.0.0.21 to 1.0.0.22 upgrade
 * 
 * Remove the CUSS grid definitions after a default update
 * 
 */ 

$installer = $this;
$installer->startSetup();

$installer->run("DELETE FROM " . $this->getTable('core/config_data') . " WHERE path LIKE '%customerconnect_enabled_messages/CUSS_request/grid_config%'");

$installer->endSetup();