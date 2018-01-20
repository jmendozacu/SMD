<?php

/**
 * Version 1.0.0.30 to 1.0.0.31 upgrade
 * 
 * Fixing typo in config
 */

$installer = $this;
$installer->startSetup();

$installer->run("UPDATE " . $this->getTable('core/config_data') . " SET path = REPLACE(path,'percission','precision') WHERE path like '%percission%'");

$installer->endSetup();