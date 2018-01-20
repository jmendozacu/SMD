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

class Blackbird_ContentManager_Block_Menu extends Mage_Core_Block_Template
{
    private $_menu;
    private $_nodes;
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        //Applied content layout in cascading
        $name = (strpos($this->getNameInLayout(), 'ANONYMOUS')!==false)?$this->getName():$this->getNameInLayout();
        
        //test applying menu-MENU_IDENTIFIER.phtml
        $this->setTemplate('contenttype/menu-'.$name.'.phtml');
        if(!file_exists(Mage::getBaseDir('app') . DS . 'design' . DS . $this->getTemplateFile()))
        {
            //applying default menu.phtml
            $this->setTemplate('contenttype/menu.phtml');
        }
    }
    
    public function getMenu()
    {
        if(!$this->_menu)
        {
            $name = (strpos($this->getNameInLayout(), 'ANONYMOUS')!==false)?$this->getName():$this->getNameInLayout();
        
            $model = Mage::getModel('contentmanager/menu');
            $model->load($name, 'identifier');
            
            $this->_menu = $model;
        }
        
        return $this->_menu;
    }
    
    public function getNodes()
    {
        if(!$this->_nodes)
        {
            $model = $this->getMenu();

            if (! $model->getId()) {
                $this->_nodes = array();
            }
            else{
                $this->_nodes = $model->getTreeNodes(true);
            }
        }
        
        return $this->_nodes;
    }
}