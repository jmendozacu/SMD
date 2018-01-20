<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category	Blackbird
 * @package		Blackbird_ContentManager
 * @copyright	Copyright (c) 2014 Blackbird Content Manager (http://black.bird.eu)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version		
 */

class Blackbird_ContentManager_Block_Adminhtml_Contenttype_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('contenttype_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('contentmanager')->__('Manage content type'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('contentmanager')->__('Informations'),
            'title'     => Mage::helper('contentmanager')->__('Informations'),
            'content'   => $this->getLayout()->createBlock('contentmanager/adminhtml_contenttype_edit_tab_form')->toHtml(),
        ));
        
        $this->addTab('custom_options', array(
                'label' => Mage::helper('contentmanager')->__('Manage fields'),
                'url'   => $this->getUrl('*/*/options', array('_current' => true)),
                'class' => 'ajax',
        ));
        
        $this->addTab('url_section', array(
            'label'     => Mage::helper('contentmanager')->__('URL'),
            'title'     => Mage::helper('contentmanager')->__('URL'),
            'content'   => $this->getLayout()->createBlock('contentmanager/adminhtml_contenttype_edit_tab_url')->toHtml(),
        ));
        
        $this->addTab('breadcrumb', array(
            'label'     => Mage::helper('contentmanager')->__('Breadcrumbs'),
            'title'     => Mage::helper('contentmanager')->__('Breadcrumbs'),
            'content'   => $this->getLayout()->createBlock('contentmanager/adminhtml_contenttype_edit_tab_breadcrumb')->toHtml(),
        ));
        
        
        $this->addTab('meta_section', array(
            'label'     => Mage::helper('contentmanager')->__('Default Meta tags'),
            'title'     => Mage::helper('contentmanager')->__('Default Meta tags'),
            'content'   => $this->getLayout()->createBlock('contentmanager/adminhtml_contenttype_edit_tab_meta')->toHtml(),
        ));
        
        $this->addTab('sitemap_section', array(
            'label'     => Mage::helper('contentmanager')->__('Google Sitemap'),
            'title'     => Mage::helper('contentmanager')->__('Google Sitemap'),
            'content'   => $this->getLayout()->createBlock('contentmanager/adminhtml_contenttype_edit_tab_sitemap')->toHtml(),
        ));
        
        
        /*$this->addTab('review_section', array(
                'label'     => Mage::helper('contentmanager')->__('Reviews'),
                'title'     => Mage::helper('contentmanager')->__('Reviews'),
                'content'   => $this->getLayout()->createBlock('contentmanager/adminhtml_contenttype_edit_tab_review')->toHtml(),
        ));*/
        
        $this->addTab('search_section', array(
                'label'     => Mage::helper('contentmanager')->__('Search'),
                'title'     => Mage::helper('contentmanager')->__('Search'),
                'content'   => $this->getLayout()->createBlock('contentmanager/adminhtml_contenttype_edit_tab_search')->toHtml(),
        ));
        
        $this->addTab('layout_section', array(
                'label'     => Mage::helper('contentmanager')->__('Layout'),
                'title'     => Mage::helper('contentmanager')->__('Layout'),
                'content'   => $this->getLayout()->createBlock('contentmanager/adminhtml_contenttype_edit_tab_layout')->toHtml(),
        ));
        
        
        return parent::_beforeToHtml();
    }
}