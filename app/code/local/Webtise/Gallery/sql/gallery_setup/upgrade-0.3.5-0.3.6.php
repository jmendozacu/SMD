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
$setup->addAttribute('gallery_gallery', 'sort_order', array(
    'group'         => 'General',
    'label'         => 'Sort Order',
    'type'          => 'text',
    'input'         => 'text',
    'backend'       => '',
    'required'      => true,
    'sort_order'    => 100
));

$this->endSetup();