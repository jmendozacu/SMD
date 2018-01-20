<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:43
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct() {
        parent::__construct();
        $this->setId('gallery_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('gallery')->__('Gallery Information'));
    }

    protected function _prepareLayout(){
        $gallery = $this->getGallery();
        $entity = Mage::getModel('eav/entity_type')->load('gallery_gallery', 'entity_type_code');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entity->getEntityTypeId());
        $attributes->addFieldToFilter('attribute_code', array('nin'=>array('media_gallery', 'category_ids')));
        $attributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab('info', array(
            'label'     => Mage::helper('gallery')->__('Gallery Information'),
            'content'   => $this->getLayout()->createBlock('gallery/adminhtml_gallery_edit_tab_form')
                ->setAttributes($attributes)
                ->toHtml(),
        ));

        $cmsAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entity->getEntityTypeId())
            ->addFieldToFilter('attribute_code', array('in'=>array('pages')));
        $cmsAttributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab('cms', array(
            'label'     => Mage::helper('gallery')->__('CMS Page Display'),
            'content'   => $this->getLayout()->createBlock('gallery/adminhtml_gallery_edit_tab_pages')
                ->setAttributes($cmsAttributes)
                ->toHtml(),
        ));

        $catAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entity->getEntityTypeId())
            ->addFieldToFilter('attribute_code', array('in'=>array('category_ids')));
        $catAttributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab('categories', array(
            'label'     => Mage::helper('gallery')->__('Category Page Display'),
            'title'     => Mage::helper('gallery')->__('Category Page Display'),
            'content'   => $this->getLayout()->createBlock('gallery/adminhtml_gallery_edit_tab_categories')
                ->setAttributes($catAttributes)
                ->toHtml(),
        ));

        $this->addTab('products', array(
            'label'     => Mage::helper('gallery')->__('Product Page Display'),
            'title'     => Mage::helper('gallery')->__('Product Page Display'),
            'url'       => $this->getUrl('*/*/product', array('_current' => true)),
            'class'     => 'ajax'
        ));

        $galleryAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entity->getEntityTypeId())
            ->addFieldToFilter('attribute_code', array('in'=>array('media_gallery')));
        $galleryAttributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab('gallery_images', array(
            'label'     => Mage::helper('gallery')->__('Gallery Images'),
            'title'     => Mage::helper('gallery')->__('Gallery Images'),
            'content'   => $this->getLayout()->createBlock('gallery/adminhtml_gallery_edit_tab_gallery')
                ->setAttributes($galleryAttributes)
                ->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

    public function getGallery(){
        return Mage::registry('current_gallery');
    }
}