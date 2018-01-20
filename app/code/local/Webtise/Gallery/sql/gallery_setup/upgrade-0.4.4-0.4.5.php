<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 21/06/2016
 * Time: 10:57
 */



$this->startSetup();

#----------------------------------------------------------------------------------------
# New Gallery Attribute Creation
# - category_ids
#----------------------------------------------------------------------------------------

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('gallery_gallery', 'gallery_html', array(
    'group'                     => 'General',
    'label'                     => 'Gallery html',
    'type'                      => 'text',
    'input'                     => 'textarea',
    'is_wysiwyg_enabled'        => 1,
    'is_html_allowed_on_front'  => true,
    'backend'                   => '',
    'required'                  => false,
    'sort_order'                => 100
));

$this->endSetup();