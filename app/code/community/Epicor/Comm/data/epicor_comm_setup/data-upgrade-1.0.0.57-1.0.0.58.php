<?php

/**
 * Version 1.0.0.57 to 1.0.0.58 upgrade
 * 
 * Deleting field mapping from the database, so that all systems use the defaults
 */

$installer = $this;
$installer->startSetup();

$installer->run("UPDATE " . $this->getTable('core/config_data') . " SET path = REPLACE(path,'epicor_comm_filed_mapping','epicor_comm_field_mapping') WHERE path like '%epicor_comm_filed_mapping%'");

$installer->endSetup();