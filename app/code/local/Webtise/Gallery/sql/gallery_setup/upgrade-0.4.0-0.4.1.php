<?php

$this->startSetup();

#----------------------------------------------------------------------------------------
# New Media Gallery Attribute Creation
# - related_product_ids
#----------------------------------------------------------------------------------------

$this->getConnection()
->addColumn($this->getTable('gallery/gallery_attribute_media_gallery_value'),
'related_product_ids',
array(
'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
'nullable' => true,
'comment' => 'Related Product IDs'
)
);

$this->endSetup();