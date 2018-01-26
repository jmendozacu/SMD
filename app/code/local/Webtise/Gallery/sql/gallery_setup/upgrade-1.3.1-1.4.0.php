<?php
/**
 * Created by PhpStorm.
 * User: joshuacarter
 * Date: 23/01/2018
 * Time: 16:47
 */

$this->startSetup();

#----------------------------------------------------------------------------------------
# New Media Gallery Attribute Creation
# - description
#----------------------------------------------------------------------------------------

$this->getConnection()
    ->addColumn($this->getTable('gallery/gallery_attribute_media_gallery_value'),
        'description',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Description'
        )
    );

$this->endSetup();
