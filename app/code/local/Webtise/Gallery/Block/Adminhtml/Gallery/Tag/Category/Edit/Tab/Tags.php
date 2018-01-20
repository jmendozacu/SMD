<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 22/06/2016
 * Time: 14:48
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Tag_Category_Edit_Tab_Tags extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tagsGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setDefaultFilter(array('in_tags' => 1));
        $this->setSaveParametersInSession(false);
    }

    protected function _addColumnFilterToCollection($column)
    {
        if($column->getId() == 'in_tags') {
            $tagIds = $this->_getSelectedTags();
            if(empty($tagIds)) {
                $tagIds = 0;
            }
            if($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $tagIds));
            } else {
                if($tagIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $tagIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('gallery/gallery_tag')->getCollection()
            ->addFieldToSelect('*');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){
        $this->addColumn('in_tags', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'name'              => 'tag',
            'values'            => $this->_getSelectedTags(),
            'align'             => 'center',
            'index'             => 'entity_id'
        ));
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('gallery')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'entity_id'
        ));
        $this->addColumn('title', array(
            'header'    => Mage::helper('gallery')->__('Title'),
            'index'     => 'title'
        ));
        $this->addColumn('status', array(
            'header'    => Mage::helper('gallery')->__('Status'),
            'width'     => 90,
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray()
        ));
        return parent::_prepareColumns();
    }

    protected function _getSelectedTags() {
        $products = array_keys($this->getSelectedTags());
        return $products;
    }

    public function getSelectedTags() {
        $tagCategory = Mage::registry('current_gallery_tag_category');
        if($tagCategory) {
            $selectedTags = explode(',', $tagCategory->getTagIds());
            $tags = Mage::getModel('gallery/gallery_tag')->getCollection()
                ->addFieldToFilter('entity_id', array('in', $selectedTags));
        } else {
            $tags = Mage::getModel('gallery/gallery_tag')->getCollection();
        }
        $tagIds = array();
        foreach($tags as $tag) {
            $tagIds[$tag->getId()] = 0;
        }
        return $tagIds;
    }

    public function getGridUrl()
    {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/taggrid',  array('_current' => true));
    }
}