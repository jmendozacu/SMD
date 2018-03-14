<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

/* @var $installer MageWorx_Downloads_Model_Resource_Setup */
$installer = $this;
$installer->installEntities();

$installer->startSetup();

$storeId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
if (Mage::app()->isSingleStoreMode()) {
    $storeId = Mage::app()->getStore(true)->getId();
}

$conn = $installer->getConnection();

// 1.7.0

if ($installer->tableExists($this->getTable('downloads_categories')) && !$installer->tableExists($this->getTable('mageworx_downloads_categories'))) {
    $installer->run("RENAME TABLE {$this->getTable('downloads_categories')} TO {$this->getTable('mageworx_downloads/categories')};");
}

if ($installer->tableExists($this->getTable('downloads_files')) && !$installer->tableExists($this->getTable('mageworx_downloads_files'))) {
    $installer->run("RENAME TABLE {$this->getTable('downloads_files')} TO {$this->getTable('mageworx_downloads/files')};");
}

if ($installer->tableExists($this->getTable('downloads_relation')) && !$installer->tableExists($this->getTable('mageworx_downloads_relation'))) {
    $installer->run("RENAME TABLE {$this->getTable('downloads_relation')} TO {$this->getTable('mageworx_downloads/relation')};");
}

if ($installer->tableExists($this->getTable('downloads_customer')) && !$installer->tableExists($this->getTable('mageworx_downloads_customer'))) {
    $installer->run("RENAME TABLE {$this->getTable('downloads_customer')} TO {$this->getTable('mageworx_downloads/customer')};");
}


// 1.0.0

$installer->run(
    "
-- DROP TABLE IF EXISTS {$this->getTable('mageworx_downloads/categories')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mageworx_downloads/categories')} (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` smallint(6) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `is_active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
"
);

if ($conn->tableColumnExists($this->getTable('mageworx_downloads/categories'), 'store_ids')) {
    $installer->run(
        "
        INSERT IGNORE INTO {$this->getTable('mageworx_downloads/categories')} (`category_id`,`store_id`,`title`,`description`,`is_active`) VALUES
          (" . MageWorx_Downloads_Helper_Data::DEFAULT_CATEGORY_ID . "," . $storeId . ",'Default','Default category'," . MageWorx_Downloads_Helper_Data::STATUS_ENABLED . ");
    "
    );
}

$installer->run(
    "
-- DROP TABLE IF EXISTS {$installer->getTable('mageworx_downloads/files')};
CREATE TABLE IF NOT EXISTS {$installer->getTable('mageworx_downloads/files')} (
  `file_id` int(10) unsigned NOT NULL auto_increment,
  `category_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_description` text,
  `type` varchar(10) NOT NULL,
  `size` int(10) unsigned NOT NULL default '0',
  `allow_guests` tinyint(1) unsigned NOT NULL default '1',
  `customer_groups` text,
  `downloads` int(10) unsigned NOT NULL,
  `downloads_limit` int(10) unsigned NOT NULL,
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL default '0',
   PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('mageworx_downloads/relation')};
CREATE TABLE IF NOT EXISTS {$installer->getTable('mageworx_downloads/relation')} (
  `id` int(10) unsigned NOT NULL auto_increment,
  `file_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `UNQ_MAGEWORX_DOWNLOADS_RELATION` (`file_id`,`product_id`),
   CONSTRAINT `FK_MAGEWORX_DOWNLOADS_PRODUCT_ENTITY` FOREIGN KEY (`product_id`) REFERENCES `{$installer->getTable('catalog/product')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
"
);

$installer->run(
    "
-- DROP TABLE IF EXISTS {$this->getTable('mageworx_downloads/customer')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mageworx_downloads/customer')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `download_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `store_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
"
);

// 1.1.2 > 1.2.0

$installer->getConnection()->addColumn(
    $installer->getTable('mageworx_downloads/files'),
    'url',
    "varchar (1024) default ''"
);

$installer->getConnection()->addColumn(
    $installer->getTable('mageworx_downloads/files'),
    'embed_code',
    "text default ''"
);

$installer->getConnection()->addColumn(
    $installer->getTable('mageworx_downloads/files'),
    'filename',
    "text default ''"
);


// 1.3.9 > 1.4.0

$installer->getConnection()->addColumn(
    $installer->getTable('mageworx_downloads/files'),
    'store_ids',
    "varchar (255) default 0"
);

$installer->getConnection()->dropColumn(
    $installer->getTable('mageworx_downloads/categories'),
    'store_id'
);

$importDir = Mage::getBaseDir('media') . DS . 'downloads_import';
if (!is_dir($importDir)) {
    mkdir($importDir, 0777, true);
}


// 1.4.2 > 1.4.3

$installer->getConnection()->changeColumn(
    $installer->getTable('mageworx_downloads/files'),
    'store_ids',
    'store_ids',
    "varchar (255) NOT NULL DEFAULT 0"
);


// 1.5.1 > 1.6.0

$installer->getConnection()->addColumn(
    $installer->getTable('mageworx_downloads/files'),
    'is_attach',
    "tinyint(1) unsigned  default 0"
);

// 1.6.0 > 1.7.0

$installer->getConnection()->addColumn(
    $installer->getTable('mageworx_downloads/customer'),
    'total_downloads',
    "int(11) unsigned default 1"
);

// updating config paths

$installer->run("UPDATE IGNORE `{$this->getTable('core_config_data')}` SET `path` = REPLACE(`path`,'mageworx_cms/downloads/','mageworx_downloads/main/') WHERE `path` LIKE 'mageworx_cms/downloads/%'");

$installer->endSetup();
