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

class Blackbird_ContentManager_Block_Adminhtml_Menu_Edit_Renderer_Tree
    extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Form element which re-rendering
     *
     * @var Varien_Data_Form_Element_Fieldset
     */
    protected $_element;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->setTemplate('blackbird/contentmanager/menu/form/renderer/tree.phtml');
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
    
    public function generateJs($nodes)
    {
        $result = '';
        if(isset($nodes))
        {
            foreach($nodes as $node)
            {
                if($node['format'])
                {
                    $format = unserialize($node['format']);
                }
                $result .= "addCtMenuNode('".$node['node_id']."', '".$node['menu_id']."', '".$node['parent_id']."', '".$node['type']."', '".$node['entity_id']."', '".addslashes($node['label'])."', '".addslashes($node['format'])."', '".$node['status']."', '".(($node['status'])?$this->__('Enabled'):$this->__('Disabled'))."', '".$node['position']."', '".$node['level']."', '".$node['target']."', '".$node['classes']."', '".$format['firstchild']."', '".addslashes(str_replace('"', '&quot;', $format['url']))."', '".$node['url_path']."', '".$node['canonical']."');\n";
                if(isset($node['children']) && is_array($node['children']))
                { 
                    $result .= $this->generateJs($node['children']);
                }
            }
        }
        
        return $result;
    }
}
