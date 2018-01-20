<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 21/06/2016
 * Time: 10:57
 */



$this->startSetup();

// Update Gallery HTML Class Attribute to not be required

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->updateAttribute('gallery_gallery', 'gallery_class', 'is_required', false);

$this->endSetup();