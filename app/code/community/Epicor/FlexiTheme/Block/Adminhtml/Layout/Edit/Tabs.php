<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    /**
     * Initialize Tabs
     *
     */
    // protected $_attributeTabBlock = 'epicor_comm/block_adminhtml_access_right_edit_tab_details';

    public function __construct()
    {
        parent::__construct();
        $this->setId('form_tabs');
        $this->setDestElementId('edit_form'); // this should be same as the form id define above
        $this->setTitle('Theme Layout');
    }

    protected function _beforeToHtml()
    {

        $layout = Mage::registry('layout_data');
        /* @var $layout_page_blocks Epicor_FlexiTheme_Model_Mysql4_Layout_Template_Section_Collection */
        $sections = Mage::getModel('flexitheme/layout_template_section')->getCollection();
        $sections->getSelect()->group('section_name');

        $block = $this->getLayout()->createBlock('flexitheme/adminhtml_layout_edit_tab_details');

        $this->addTab('form_details', array(
            'label' => 'General',
            'title' => 'General Information',
            'content' => $block->toHtml(),
        ));

        $pages = Mage::getModel('flexitheme/layout_page')->getCollection();

        foreach ($pages as $page) {
            $page_type = 'std_page';
            if (strtolower($page->getPageName()) == 'default')
                $page_type = 'def_page';

            $block = $this->getLayout()->createBlock('flexitheme/adminhtml_layout_edit_tab_page', '', array(
                'sections' => $sections,
                'layout_id' => $layout->getId(),
                'page_id' => $page->getId(),
                'page_type' => $page_type,
            ));
            $this->addTab('form_' . Mage::helper('flexitheme')->safeString($page->getPageName()) . '_layout', array(
                'label' => $page->getPageName() . ' Layout',
                'title' => $page->getPageName() . ' Layout',
                'content' => $block->toHtml(),
            ));
        }

        $cmsPages = Mage::getModel('cms/page')->getCollection();
        /* @var $cms_page Mage_Cms_Model_Mysql4_Page_Collection */

        $urls = array();

        foreach ($cmsPages as $cmsPage) {

            $block = $this->getLayout()->createBlock(
                'flexitheme/adminhtml_layout_edit_tab_page', 
                '', 
                array(
                    'sections' => $sections,
                    'layout_id' => $layout->getId(),
                    'cms_page_identifier' => $cmsPage->getIdentifier(),
                    'page_type' => 'cms_page',
                )
            );
            
            if (!in_array($cmsPage->getIdentifier(), $urls)) {
                $this->addTab(
                    'form_' . Mage::helper('flexitheme')->safeString($cmsPage->getTitle()) . '_layout', 
                    array(
                        'label' => $cmsPage->getTitle() . ' Layout',
                        'title' => $cmsPage->getTitle() . ' Layout',
                        'content' => $block->toHtml(),
                    )
                );
                $urls[] = $cmsPage->getIdentifier();
            }
        }

        return parent::_beforeToHtml();
    }

}
