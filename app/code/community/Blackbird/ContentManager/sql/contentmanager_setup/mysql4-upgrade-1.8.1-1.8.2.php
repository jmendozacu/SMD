<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   Copyright (c) 2013 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$version = Mage::getConfig()->getModuleConfig("Mage_Admin")->version;
if(version_compare($version, '1.6.1.2', '>='))
{
    $installer->getConnection()->insertMultiple(
        $installer->getTable('admin/permission_block'),
        array(
            array('block_name' => 'contentmanager/list', 'is_allowed' => 1),
            array('block_name' => 'contentmanager/view', 'is_allowed' => 1),
            array('block_name' => 'contentmanager/menu', 'is_allowed' => 1),
            array('block_name' => 'contentmanager/filter', 'is_allowed' => 1),
        )
    );
}

$installer->endSetup();
