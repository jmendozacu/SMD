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
    ->addColumn($this->getTable('cms/page'),
        'is_gallery_page',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable'  => false,
            'default'   => '0',
            'comment'   => 'Is Page a Gallery Page'
        )
    );
$this->getConnection()
    ->addColumn($this->getTable('cms/page'),
        'show_tag_navigation',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable'  => false,
            'default'   => '0',
            'comment'   => 'Show Tag Layered Navigation'
        )
    );

$this->endSetup();