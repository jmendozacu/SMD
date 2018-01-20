<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:46
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Attribute_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gallery_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('gallery')->__('Attribute Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main', array(
            'label'     => Mage::helper('gallery')->__('Properties'),
            'title'     => Mage::helper('gallery')->__('Properties'),
            'content'   => $this->getLayout()->createBlock('gallery/adminhtml_gallery_attribute_edit_tab_main')->toHtml(),
            'active'    => true
        ));
        $this->addTab('labels', array(
            'label'     => Mage::helper('gallery')->__('Manage Label / Options'),
            'title'     => Mage::helper('gallery')->__('Manage Label / Options'),
            'content'   => $this->getLayout()->createBlock('gallery/adminhtml_gallery_attribute_edit_tab_options')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}