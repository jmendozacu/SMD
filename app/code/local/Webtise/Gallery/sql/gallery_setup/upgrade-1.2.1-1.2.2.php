<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 21/06/2016
 * Time: 10:57
 */



$this->startSetup();

#----------------------------------------------------------------------------------------
# New Media Gallery Attribute Creation
# - tag
#----------------------------------------------------------------------------------------

$this->getConnection()
    ->addColumn($this->getTable('gallery/gallery_attribute_media_gallery_value'),
        'tag_ids',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Tag Ids'
        )
    );

$this->endSetup();