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

$pageTable = $installer->getTable('contentmanager/contenttype');

$installer->getConnection()->addColumn($pageTable, 'sitemap_enable',
    "tinyint(1) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($pageTable, 'sitemap_frequency',
    "varchar(50) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($pageTable, 'sitemap_priority',
    "varchar(10) NOT NULL DEFAULT '0'");

$pageTable = $installer->getTable('contentmanager/menu');
$installer->getConnection()->addColumn($pageTable, 'sitemap_enable',
    "tinyint(1) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($pageTable, 'sitemap_frequency',
    "varchar(50) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($pageTable, 'sitemap_priority',
    "varchar(10) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($pageTable, 'sitemap_frequency_level1',
    "varchar(50) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($pageTable, 'sitemap_priority_level1',
    "varchar(10) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($pageTable, 'sitemap_frequency_level2',
    "varchar(50) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($pageTable, 'sitemap_priority_level2',
    "varchar(10) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($pageTable, 'sitemap_frequency_level3',
    "varchar(50) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($pageTable, 'sitemap_priority_level3',
    "varchar(10) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($pageTable, 'sitemap_frequency_level4',
    "varchar(50) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($pageTable, 'sitemap_priority_level4',
    "varchar(10) NOT NULL DEFAULT '0'");

$installer->endSetup();
