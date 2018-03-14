<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

/** @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

// insert cms blocks to whitelist
$newRows = array(
    array('block_name' => 'mageworx_downloads/link', 'is_allowed' => 1),
    array('block_name' => 'mageworx_downloads/category_link', 'is_allowed' => 1),
    array('block_name' => 'mageworx_downloads/product_link', 'is_allowed' => 1),
);

if (version_compare(Mage::getConfig()->getModuleConfig('Mage_Admin')->version, '1.6.1.2', 'ge')) {
    foreach ($newRows as $row) {
        $installer->getConnection()->insertIgnore($installer->getTable('admin/permission_block'), $row);    
    }
}

$installer->endSetup();