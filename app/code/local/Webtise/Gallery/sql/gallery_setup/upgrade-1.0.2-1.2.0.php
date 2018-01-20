<?php
/**
 * Create table 'gallery/gallery_tag_category'
 */
$tagCategoryTable = $this->getConnection()
    ->newTable($this->getTable('gallery/gallery_tag_category'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Title')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Code')
    ->addColumn('tag_ids', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Associated Tags')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Status')
    ->addIndex($this->getIdxName('gallery/gallery_tag_category', array('entity_id')),
        array('entity_id'))
    ->setComment('Webtise Gallery Tag Category Table');
$this->getConnection()->createTable($tagCategoryTable);

/**
 * Create table 'gallery/gallery_tag'
 */
$tagTable = $this->getConnection()
    ->newTable($this->getTable('gallery/gallery_tag'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Title')
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Image')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Status')
    ->addIndex($this->getIdxName('gallery/gallery_tag', array('entity_id')),
        array('entity_id'))
    ->setComment('Webtise Gallery Tag Table');
$this->getConnection()->createTable($tagTable);
