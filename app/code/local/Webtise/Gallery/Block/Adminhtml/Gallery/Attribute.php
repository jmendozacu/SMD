<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:44
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_gallery_attribute';
        $this->_blockGroup = 'gallery';
        $this->_headerText = Mage::helper('gallery')->__('Manage Gallery Attributes');
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('gallery')->__('Add New Gallery Attribute'));
    }
}