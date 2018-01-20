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
# - show_on_cms
# - cms_type
#----------------------------------------------------------------------------------------

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute('gallery_gallery', 'show_on_cms', array(
    'group'         => 'General',
    'label'         => 'Show On CMS Pages',
    'type'          => 'varchar',
    'input'         => 'select',
    'source'        => 'eav/entity_attribute_source_boolean',
    'backend'       => '',
    'required'      => true,
    'sort_order'    => 10
));
$setup->addAttribute('gallery_gallery', 'cms_type', array(
    'group'         => 'General',
    'label'         => 'Which CMS Pages',
    'type'          => 'varchar',
    'input'         => 'select',
    'source'        => 'gallery/source_cms_type',
    'backend'       => '',
    'required'      => true,
    'sort_order'    => 10
));

$this->endSetup();