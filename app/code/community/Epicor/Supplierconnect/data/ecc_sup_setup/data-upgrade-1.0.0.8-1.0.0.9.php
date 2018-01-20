<?php

/**
 * Version 1.0.0.7 to 1.0.0.8 upgrade
 * 
 * removing old config so that default grid config can be updated by config 
 * (Response column being searched by Magento)
 */
$installer = $this;
$installer->startSetup();

$installer->run("DELETE FROM " . $this->getTable('core/config_data') . " WHERE path LIKE '%SPOS_request/grid_config%'");
$installer->run("DELETE FROM " . $this->getTable('core/config_data') . " WHERE path LIKE '%SPOS_request/newpogrid_config%'");
$installer->run("DELETE FROM " . $this->getTable('core/config_data') . " WHERE path LIKE '%SPCS_request/grid_config%'");

$installer->endSetup();