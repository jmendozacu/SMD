<?php

/**
 * 1.0.5.15 - magento downloader fix for the mage_all_latest.txt file being a 
 * folder due to not existing in the package
 * 
 * Fixes a small error in the downloader file to stop files being folders issue
 */
$helper = Mage::helper('epicor_upgrade');
/* @var $helper Epicor_Upgrade_Helper_Data */

$helper->fixMageAllLatestFile();
