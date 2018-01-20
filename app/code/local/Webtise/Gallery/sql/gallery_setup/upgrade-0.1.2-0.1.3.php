<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 02/03/2016
 * Time: 14:45
 */


$this->startSetup();

#----------------------------------------------------------------------------------------
# New Gallery Attribute Creation
# - is_generic_url
# - generic_url
#----------------------------------------------------------------------------------------

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute('gallery_gallery', 'is_generic_url', array(
        'group'         => 'General',
        'label'         => 'Generic Url',
        'type'          => 'varchar',
        'input'         => 'select',
        'source'        => 'eav/entity_attribute_source_boolean',
        'backend'       => '',
        'required'      => true,
        'sort_order'    => 10
));
$setup->addAttribute('gallery_gallery', 'generic_url', array(
        'group'         => 'General',
        'label'         => 'Url',
        'type'          => 'varchar',
        'input'         => 'text',
        'backend'       => '',
        'required'      => true,
        'sort_order'    => 20
));

$this->endSetup();