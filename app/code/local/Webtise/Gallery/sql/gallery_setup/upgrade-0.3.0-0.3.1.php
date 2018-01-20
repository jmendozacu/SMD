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
# - image_specific_url
#----------------------------------------------------------------------------------------

$this->getConnection()
    ->addColumn($this->getTable('gallery/gallery_attribute_media_gallery_value'),
        'image_specific_url',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Image Specific Url'
        )
    );

$this->endSetup();