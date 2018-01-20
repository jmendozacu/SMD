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

class Blackbird_ContentManager_Block_Adminhtml_Content_Edit_Renderer_Menu
    extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Form element which re-rendering
     *
     * @var Varien_Data_Form_Element_Fieldset
     */
    protected $_element;
    protected $_menu;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->setTemplate('blackbird/contentmanager/content/form/renderer/menu.phtml');
    }

    /**
     * Retrieve an element
     *
     * @return Varien_Data_Form_Element_Fieldset
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Render element
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }
    
    /**
     * Get menu once per menu id
     * @param type $menuid
     * @return type
     */
    public function getMenu($menuid)
    {
        if(!isset($this->_menu[$menuid]))
        {
            $menu = Mage::getModel('contentmanager/menu')
                        ->getCollection()
                        ->addFieldToFilter('main_table.menu_id', $menuid)
                        ->addFieldToSelect('title')
                        ->addFieldToSelect('identifier')
                        ->getFirstItem();
                    
            $this->_menu[$menuid] = $menu;
        }
        
        return $this->_menu[$menuid];
    }
    
    /**
     * Get all node for current content
     * @return nodes collection
     */
    public function getNodes()
    {
        $nodes = Mage::getModel('contentmanager/menu_node')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('type', 'content')
            ->addFieldToFilter('entity_id', $this->getContentId());
        
        $nodes->getSelect()->order('menu_id ASC');
        
        return $nodes;
    }
}
