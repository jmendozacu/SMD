<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:43
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Tag_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gallery_tag_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('gallery')->__('Gallery Tag Information'));
    }

    /**
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    protected function _prepareLayout()
    {
        $this->addTab('info', array(
            'label'     => Mage::helper('gallery')->__('Gallery Tag Information'),
            'content'   => $this->getLayout()->createBlock('gallery/adminhtml_gallery_tag_category_edit_tab_form')
                ->toHtml(),
        ));

        $this->addTab('tags', array(
            'label'     => Mage::helper('gallery')->__('Associated Tags'),
            'title'     => Mage::helper('gallery')->__('Associated Tags'),
            'url'       => $this->getUrl('*/*/tag', array('_current' => true)),
            'class'     => 'ajax'
        ));

        return parent::_beforeToHtml();
    }
}