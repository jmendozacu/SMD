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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category	Blackbird
 * @package		Blackbird_ContentManager
 * @copyright	Copyright (c) 2014 Blackbird Content Manager (http://www.blackbird.fr)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version		
 */

class Blackbird_ContentManager_Block_Adminhtml_Content_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('contenttype_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('contentmanager')->__('Manage content'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('contentmanager')->__('Content'),
            'title'     => Mage::helper('contentmanager')->__('Content'),
            'content'   => $this->getLayout()->createBlock('contentmanager/adminhtml_content_edit_tab_form')->toHtml(),
        ));
        
        $this->addTab('title_meta_section', array(
            'label'     => Mage::helper('contentmanager')->__('Meta tags'),
            'title'     => Mage::helper('contentmanager')->__('Meta tags'),
            'content'   => $this->getLayout()->createBlock('contentmanager/adminhtml_content_edit_tab_meta')->toHtml(),
        ));
        
        $this->addTab('url_section', array(
            'label'     => Mage::helper('contentmanager')->__('URL'),
            'title'     => Mage::helper('contentmanager')->__('URL'),
            'content'   => $this->getLayout()->createBlock('contentmanager/adminhtml_content_edit_tab_url')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
}