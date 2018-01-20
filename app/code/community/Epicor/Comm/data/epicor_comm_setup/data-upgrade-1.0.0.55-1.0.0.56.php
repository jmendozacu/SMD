<?php

/**
 * Version 1.0.0.50 to 1.0.0.51 upgrade
 * 
 * Renaming filed mapping config to field mapping
 */

$installer = $this;
$installer->startSetup();

$installer->run("UPDATE " . $this->getTable('core/config_data') . " SET path = REPLACE(path,'epicor_comm_filed_mapping','epicor_comm_field_mapping') WHERE path like '%epicor_comm_filed_mapping%'");

$installer->endSetup();