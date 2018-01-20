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

$table = new Varien_Db_Ddl_Table();
$table->setName($installer->getTable('contentmanager/contentlist'));
$table2 = new Varien_Db_Ddl_Table();
$table2->setName($installer->getTable('contentmanager/contentlist_layout_block'));
$table3 = new Varien_Db_Ddl_Table();
$table3->setName($installer->getTable('contentmanager/contentlist_layout_field'));
$table4 = new Varien_Db_Ddl_Table();
$table4->setName($installer->getTable('contentmanager/contentlist_layout_group'));
$table5 = new Varien_Db_Ddl_Table();
$table5->setName($installer->getTable('contentmanager/contentlist_store'));

$table->addColumn(
    'cl_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    10,
    array(
        'auto_increment' => true,
        'unsigned' => true,
        'nullable'=> false,
        'primary' => true
    )
);
$table->addColumn(
    'title',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'url_key',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'status',
    Varien_Db_Ddl_Table::TYPE_TINYINT,
    1,
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'text_before',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    '64k',
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'text_after',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    '64k',
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'ct_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    '11',
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'limit_display',
    Varien_Db_Ddl_Table::TYPE_TINYINT,
    1,
    array(
        'unsigned' => true,
        'nullable'=> false
    )
);
$table->addColumn(
    'order_field',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'order_by',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    15,
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'pagination',
    Varien_Db_Ddl_Table::TYPE_TINYINT,
    1,
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'breadcrumb',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> true
    )
);
$table->addColumn(
    'breadcrumb_custom_title',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    '64k',
    array(
        'nullable'=> true
    )
);
$table->addColumn(
    'breadcrumb_prev_link',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    '64k',
    array(
        'nullable'=> true
    )
);
$table->addColumn(
    'breadcrumb_prev_name',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    '64k',
    array(
        'nullable'=> true
    )
);
$table->addColumn(
    'meta_title',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'meta_description',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    '64k',
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'meta_keywords',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    '64k',
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'meta_robots',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> false
    )
);

$table->addColumn(
    'og_title',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'og_url',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'og_description',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    '64k',
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'og_image',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'og_type',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'layout',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'root_template',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    100,
    array(
        'nullable'=> false
    )
);
$table->addColumn(
    'layout_update_xml',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    '64k',
    array(
        'nullable'=> false
    )
);

$table2->addColumn(
    'layout_block_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'auto_increment' => true,
        'unsigned' => true,
        'nullable'=> false,
        'primary' => true
    )
);
$table2->addColumn(
    'layout_group_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'nullable'=> true
    )
);
$table2->addColumn(
    'cl_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'unsigned'=> true,
        'nullable'=> false
    )
);
$table2->addColumn(
    'block_id',
    Varien_Db_Ddl_Table::TYPE_SMALLINT,
    6,
    array(
        'nullable'=> false
    )
);
$table2->addColumn(
    'html_tag',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    50,
    array(
        'nullable'=> true
    )
);
$table2->addColumn(
    'html_id',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> true
    )
);
$table2->addColumn(
    'html_class',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> true
    )
);
$table2->addColumn(
    'label',
    Varien_Db_Ddl_Table::TYPE_SMALLINT,
    3,
    array(
        'nullable'=> true
    )
);
$table2->addColumn(
    'column',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'nullable'=> true
    )
);
$table2->addColumn(
    'sort_order',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'nullable'=> true
    )
);
$table2->addColumn(
    'html_label_tag',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    50,
    array(
        'nullable'=> true,
        'default' => "div"
    )
);

$table3->addColumn(
    'layout_field_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'auto_increment' => true,
        'unsigned' => true,
        'nullable'=> false,
        'primary' => true
    )
);
$table3->addColumn(
    'layout_group_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'nullable'=> true
    )
);
$table3->addColumn(
    'cl_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'unsigned'=> true,
        'nullable'=> false
    )
);
$table3->addColumn(
    'option_id',
    Varien_Db_Ddl_Table::TYPE_SMALLINT,
    6,
    array(
        'nullable'=> false
    )
);
$table3->addColumn(
    'html_tag',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    50,
    array(
        'nullable'=> true
    )
);
$table3->addColumn(
    'html_id',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> true
    )
);
$table3->addColumn(
    'html_class',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> true
    )
);
$table3->addColumn(
    'format',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> true
    )
);
$table3->addColumn(
    'label',
    Varien_Db_Ddl_Table::TYPE_SMALLINT,
    3,
    array(
        'nullable'=> true
    )
);
$table3->addColumn(
    'column',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'nullable'=> true
    )
);
$table3->addColumn(
    'sort_order',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'nullable'=> true
    )
);
$table3->addColumn(
    'html_label_tag',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    50,
    array(
        'nullable'=> true,
        'default' => "div"
    )
);

$table4->addColumn(
    'layout_group_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'auto_increment' => true,
        'unsigned' => true,
        'nullable'=> false,
        'primary' => true
    )
);
$table4->addColumn(
    'parent_layout_group_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'nullable'=> true
    )
);
$table4->addColumn(
    'cl_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'unsigned'=> true,
        'nullable'=> false
    )
);
$table4->addColumn(
    'html_name',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> true
    )
);
$table4->addColumn(
    'html_tag',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    50,
    array(
        'nullable'=> true
    )
);
$table4->addColumn(
    'html_id',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> true
    )
);
$table4->addColumn(
    'html_class',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable'=> true
    )
);
$table4->addColumn(
    'label',
    Varien_Db_Ddl_Table::TYPE_SMALLINT,
    3,
    array(
        'nullable'=> true
    )
);
$table4->addColumn(
    'column',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'nullable'=> true
    )
);
$table4->addColumn(
    'sort_order',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'nullable'=> true
    )
);
$table4->addColumn(
    'html_label_tag',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    50,
    array(
        'nullable'=> true,
        'default' => "div"
    )
);

$table5->addColumn(
    'cl_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'unsigned' => true,
        'nullable'=> false,
        'primary' => true
    )
);
$table5->addColumn(
    'store_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'unsigned' => true,
        'nullable'=> false,
        'primary' => true
    )
);


$table->setOption('type', 'InnoDB');
$table->setOption('charset', 'utf8');
$table2->setOption('type', 'InnoDB');
$table2->setOption('charset', 'utf8');
$table3->setOption('type', 'InnoDB');
$table3->setOption('charset', 'utf8');
$table4->setOption('type', 'InnoDB');
$table4->setOption('charset', 'utf8');
$table5->setOption('type', 'InnoDB');
$table5->setOption('charset', 'utf8');

//add foreign keys
$table->addForeignKey("blackbird_contentmanager_contenttype_id","ct_id","blackbird_contenttype","ct_id");
$table2->addForeignKey("blackbird_contentmanager_block_cl_id","cl_id","blackbird_contenttype_list","cl_id");
$table3->addForeignKey("blackbird_contentmanager_field_cl_id","cl_id","blackbird_contenttype_list","cl_id");
$table4->addForeignKey("blackbird_contentmanager_group_cl_id","cl_id","blackbird_contenttype_list","cl_id");
$table5->addForeignKey("blackbird_contentmanager_cl_id","cl_id","blackbird_contenttype_list","cl_id");
$table5->addForeignKey("blackbird_contentmanager_store_id","store_id","core_store","store_id");

$this->getConnection()->createTable($table);
$this->getConnection()->createTable($table2);
$this->getConnection()->createTable($table3);
$this->getConnection()->createTable($table4);
$this->getConnection()->createTable($table5);

$installer->endSetup();
