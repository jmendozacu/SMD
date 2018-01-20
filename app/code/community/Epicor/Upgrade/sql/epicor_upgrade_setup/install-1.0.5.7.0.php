<?php

/**
 * 1.0.5.7 - magento downloader fix
 * 
 * Fixes a small error in the downloader file to stop files being folders issue
 */

$helper = Mage::helper('epicor_upgrade');
/* @var $helper Epicor_Upgrade_Helper_Data */

$helper->fixTarFile();