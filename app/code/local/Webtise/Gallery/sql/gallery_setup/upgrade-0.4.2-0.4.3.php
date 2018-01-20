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

$setup->addAttribute('gallery_gallery', 'gallery_class', array(
    'group'         => 'General',
    'label'         => 'Gallery html class',
    'type'          => 'text',
    'input'         => 'text',
    'backend'       => '',
    'required'      => true,
    'sort_order'    => 100
));
$setup->addAttribute('gallery_gallery', 'show_title', array(
    'group'         => 'General',
    'label'         => 'Show title on frontend',
    'type'          => 'varchar',
    'input'         => 'select',
    'source'        => 'eav/entity_attribute_source_boolean',
    'backend'       => '',
    'required'      => true,
    'sort_order'    => 10
));

$this->endSetup();