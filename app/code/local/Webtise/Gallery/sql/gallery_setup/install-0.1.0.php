<?php
$this->startSetup();

//create the entity table
$table = $this->getConnection()
    ->newTable($this->getTable('gallery/gallery'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Entity Type ID')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Attribute Set ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Update Time')
    ->addIndex($this->getIdxName('gallery/gallery', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($this->getIdxName('gallery/gallery', array('attribute_set_id')),
        array('attribute_set_id'))
    ->addForeignKey(
        $this->getFkName(
            'gallery/gallery',
            'attribute_set_id',
            'eav/attribute_set',
            'attribute_set_id'
        ),
        'attribute_set_id', $this->getTable('eav/attribute_set'), 'attribute_set_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($this->getFkName('gallery/gallery', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $this->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Gallery Table');
$this->getConnection()->createTable($table);

/**
 * Create table 'gallery/gallery_attribute_media_gallery'
 */
$table = $this->getConnection()
    ->newTable($this->getTable('gallery/gallery_attribute_media_gallery'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Value ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Attribute ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Value')
    ->addIndex($this->getIdxName('gallery/gallery_attribute_media_gallery', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($this->getIdxName('gallery/gallery_attribute_media_gallery', array('entity_id')),
        array('entity_id'))
    ->addForeignKey(
        $this->getFkName(
            'gallery/gallery_attribute_media_gallery',
            'attribute_id',
            'eav/attribute',
            'attribute_id'
        ),
        'attribute_id', $this->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $this->getFkName(
            'gallery/gallery_attribute_media_gallery',
            'entity_id',
            'catalog/product',
            'entity_id'
        ),
        'entity_id', $this->getTable('gallery/gallery'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Webtise Gallery Media Gallery Attribute Backend Table');
$this->getConnection()->createTable($table);

/**
 * Create table 'gallery/gallery_attribute_media_gallery_value'
 */
$table = $this->getConnection()
    ->newTable($this->getTable('gallery/gallery_attribute_media_gallery_value'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ), 'Value ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ), 'Store ID')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Label')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Position')
    ->addColumn('disabled', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Is Disabled')
    ->addIndex($this->getIdxName('gallery/gallery_attribute_media_gallery_value', array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $this->getFkName(
            'gallery/gallery_attribute_media_gallery_value',
            'value_id',
            'gallery/gallery_attribute_media_gallery',
            'value_id'
        ),
        'value_id', $this->getTable('gallery/gallery_attribute_media_gallery'), 'value_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $this->getFkName(
            'gallery/gallery_attribute_media_gallery_value',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $this->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Webtise Gallery Media Gallery Attribute Value Table');
$this->getConnection()->createTable($table);


$galleryEav = array();
$galleryEav['int'] = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'length'    => null,
    'comment'   => 'Gallery Datetime Attribute Backend Table'
);

$galleryEav['varchar'] = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'comment'   => 'Gallery Varchar Attribute Backend Table'
);

$galleryEav['text'] = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => '64k',
    'comment'   => 'Gallery Text Attribute Backend Table'
);

$galleryEav['datetime'] = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
    'length'    => null,
    'comment'   => 'Gallery Datetime Attribute Backend Table'
);

$galleryEav['decimal'] = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'length'    => '12,4',
    'comment'   => 'Gallery Datetime Attribute Backend Table'
);

foreach ($galleryEav as $type => $options) {
    $table = $this->getConnection()
        ->newTable($this->getTable(array('gallery/gallery', $type)))
        ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Value ID')
        ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ), 'Entity Type ID')
        ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ), 'Attribute ID')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ), 'Store ID')
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ), 'Entity ID')
        ->addColumn('value', $options['type'], $options['length'], array(
        ), 'Value')
        ->addIndex(
            $this->getIdxName(
                array('gallery/gallery', $type),
                array('entity_id', 'attribute_id', 'store_id'),
                Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
            ),
            array('entity_id', 'attribute_id', 'store_id'),
            array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
        ->addIndex($this->getIdxName(array('gallery/gallery', $type), array('store_id')),
            array('store_id'))
        ->addIndex($this->getIdxName(array('gallery/gallery', $type), array('entity_id')),
            array('entity_id'))
        ->addIndex($this->getIdxName(array('gallery/gallery', $type), array('attribute_id')),
            array('attribute_id'))
        ->addForeignKey(
            $this->getFkName(
                array('gallery/gallery', $type),
                'attribute_id',
                'eav/attribute',
                'attribute_id'
            ),
            'attribute_id', $this->getTable('eav/attribute'), 'attribute_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
            $this->getFkName(
                array('gallery/gallery', $type),
                'entity_id',
                'gallery/gallery',
                'entity_id'
            ),
            'entity_id', $this->getTable('gallery/gallery'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey($this->getFkName(array('gallery/gallery', $type), 'store_id', 'core/store', 'store_id'),
            'store_id', $this->getTable('core/store'), 'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment($options['comment']);
    $this->getConnection()->createTable($table);
}

$table = $this->getConnection()
    ->newTable($this->getTable('gallery/eav_attribute'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Attribute ID')
    ->addColumn('is_global', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Attribute scope')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Attribute position')
    ->addColumn('is_wysiwyg_enabled', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Attribute uses WYSIWYG')
    ->addColumn('is_visible', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Attribute is visible')
    ->setComment('Gallery attribute table');
$this->getConnection()->createTable($table);

$this->installEntities();
$this->endSetup();