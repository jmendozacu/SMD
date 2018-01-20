<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 21/06/2016
 * Time: 10:57
 */

#----------------------------------------------------------------------------------------
# New CMS Page Columns
# - is_gallery_page
# - show_tag_navigation
#----------------------------------------------------------------------------------------

$this->startSetup();

$this->getConnection()
    ->addColumn($this->getTable('gallery/gallery_tag_category'),
        'frontend_display',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => false,
            'comment'   => 'Frontend Display'
        )
    );

$this->endSetup();