<?php

/**
 * Version 1.0.0.5 to 1.0.0.6 upgrade
 * 
 * removing old config so that default grid config can be updated by config 
 * (Response column being searched by Magento)
 */
$installer = $this;
$installer->startSetup();

$installer->run("DELETE FROM " . $this->getTable('core/config_data') . " WHERE path LIKE '%SURS_request/grid_config%'");

$installer->endSetup();