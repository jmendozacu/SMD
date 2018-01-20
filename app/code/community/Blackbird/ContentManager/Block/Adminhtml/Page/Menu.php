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

class Blackbird_ContentManager_Block_Adminhtml_Page_Menu extends Mage_Adminhtml_Block_Page_Menu
{

    /**
     * Retrieve Adminhtml Menu array
     *
     * @return array
     */
    public function getMenuArray()
    {
        $menu = $this->_buildMenuArray();
        $this->_addCctMenus($menu);
        return $menu;
    }
    
    /**
     * Add CT menus to the Admin menu
     */
    private function _addCctMenus(&$menu)
    {
        //Retrieve CT collection
        $collection = Mage::getModel('contentmanager/contenttype')->getCollection();
        if($collection->count() > 0)
        {
            //Add Menus
            $i = 1;
            $hasCctShowed = false;
            foreach($collection as $contentType)
            {
                $aclResource = 'admin/contentmanager/content_'.$contentType->getIdentifier();
                $aclResourceAll = 'admin/contentmanager/content_everything';
                
                if (!$this->_checkAcl($aclResource) && !$this->_checkAcl($aclResourceAll)) {
                    continue;
                }
                
                //Root node for this content type
                $menu['cms']['children']['contenttype_'.$contentType->getIdentifier()] = array(
                    'label' => $contentType->getTitle(),
                    'url' => Mage::helper("adminhtml")->getUrl("adminhtml/contenttype_content/",array("ct_id"=>$contentType->getId())),
                    'level' => 1,
                    'active' => 0,
                    'last' => ($i == $collection->count())?1:0
                );
                
                
                //Entree Add
                $menu['cms']['children']['contenttype_'.$contentType->getIdentifier()]['children']['new'] = array(
                    'label' => $this->__('Add item'),
                    'url' => Mage::helper("adminhtml")->getUrl("adminhtml/contenttype_content/new",array("ct_id"=>$contentType->getId())),
                    'level' => 2,
                    'active' => 0,
                    'last' => 0
                );
                
                //Entree List items
                $menu['cms']['children']['contenttype_'.$contentType->getIdentifier()]['children']['list'] = array(
                    'label' => $this->__('List items'),
                    'url' => Mage::helper("adminhtml")->getUrl("adminhtml/contenttype_content/index",array("ct_id"=>$contentType->getId())),
                    'level' => 2,
                    'active' => 0,
                    'last' => 1
                );
                
                $hasCctShowed = true;
                $i++;
            }
            
            //Remove 'last' tag from CMS menu
            if($hasCctShowed)
            {
                foreach($menu['cms']['children'] as $key => &$children)
                {
                    if(!preg_match("/contenttype_/", $key))
                    {
                        unset($children['last']);                    
                    }
                }
            }
        }
            
    }

}
