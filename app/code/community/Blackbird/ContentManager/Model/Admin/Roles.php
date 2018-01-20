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

class Blackbird_ContentManager_Model_Admin_Roles extends Mage_Admin_Model_Roles
{
    
    

    /**
     * Return tree of acl resources
     *
     * @return array|null|Varien_Simplexml_Element
     */
    public function getResourcesTree()
    {
        $result = $this->_buildResourcesArray(null, null, null, null, true);
        $this->_addCctMenusXml($result);
        return $result;
    }

    /**
     * Return list of acl resources
     *
     * @return array|null|Varien_Simplexml_Element
     */
    public function getResourcesList()
    {
        $result = $this->_buildResourcesArray();
        $this->_addCctMenus($result);
        return $result;
    }

    /**
     * Return list of acl resources in 2D format
     *
     * @return array|null|Varien_Simplexml_Element
     */
    public function getResourcesList2D()
    {
        $result = $this->_buildResourcesArray(null, null, null, true);
        $this->_addCctMenus2D($result);
        return $result;
    }
    

    /**
     * Add CT menus 
     */
    public function _addCctMenus2D(&$result)
    {
        $stores = Mage::getModel('core/store')->getCollection();
            
        $collection = Mage::getModel('contentmanager/contenttype')->getCollection();
        $result[] = 'admin/contentmanager/content_everything';
        foreach($collection as $contentType)
        {
            $result[] = 'admin/contentmanager/content_'.$contentType->getIdentifier();     
            
            $result[] = 'admin/contentmanager/content_'.$contentType->getIdentifier().'_view_0';
            $result[] = 'admin/contentmanager/content_'.$contentType->getIdentifier().'_edit_0';
            //loop store views
            foreach($stores as $store)
            {
                $result[] = 'admin/contentmanager/content_'.$contentType->getIdentifier().'_view_'.$store->getId();
                $result[] = 'admin/contentmanager/content_'.$contentType->getIdentifier().'_edit_'.$store->getId();
            }
        }
        
        //Manage menus permissions by storeview
        $result[] = 'admin/contentmanager/manage_menu_0';
        foreach($stores as $store)
        {
            $result[] = 'admin/contentmanager/manage_menu_'.$store->getId();
        }
        
        return $result;
    }    
    
    /**
     * Add CT menus 
     */
    public function _addCctMenus(&$result)
    {
        $collection = Mage::getModel('contentmanager/contenttype')->getCollection();
        $stores = Mage::getModel('core/store')->getCollection();
            
        $result['admin/contentmanager/content_everything'] = array(
            'name' => 'Content Manager - All content types',
            'level' => 6
        );            
        foreach($collection as $contentType)
        {
            $result['admin/contentmanager/content_'.$contentType->getIdentifier()] = array(
                'name' => $contentType->getTitle(),
                'level' => 6
            );      
            
            //loop store views
            $result['admin/contentmanager/content_'.$contentType->getIdentifier().'_view_0'] = array(
                'name' => Mage::helper('contentmanager')->__('All').' - '.Mage::helper('contentmanager')->__('View'),
                'level' => 7
            );  
            $result['admin/contentmanager/content_'.$contentType->getIdentifier().'_edit_0'] = array(
                'name' => Mage::helper('contentmanager')->__('All').' - '.Mage::helper('contentmanager')->__('Edit'),
                'level' => 7
            );
            
            foreach($stores as $store)
            {
                $result['admin/contentmanager/content_'.$contentType->getIdentifier().'_view_'.$store->getId()] = array(
                    'name' => $store->getName().' - '.Mage::helper('contentmanager')->__('View'),
                    'level' => 7
                );  
                $result['admin/contentmanager/content_'.$contentType->getIdentifier().'_edit_'.$store->getId()] = array(
                    'name' => $store->getName().' - '.Mage::helper('contentmanager')->__('Edit'),
                    'level' => 7
                );
            }
        }
        
        //Manage menus permissions by storeview
        $result['admin/contentmanager/manage_menu_0'] = array(
            'name' => Mage::helper('contentmanager')->__('All'),
            'level' => 7
        );
        foreach($stores as $store)
        {
            $result['admin/contentmanager/manage_menu_'.$store->getId()] = array(
                'name' => $store->getName(),
                'level' => 7
            );
        }
        
        
        return $result;
    }
    
    /**
     * Add CT menus 
     */
    public function _addCctMenusXml(&$result)
    {
        if(!isset($result->admin) || !isset($result->admin->children) || !isset($result->admin->children->cms))
        {
            return;
        }
        
        $collection = Mage::getModel('contentmanager/contenttype')->getCollection();
        $stores = Mage::getModel('core/store')->getCollection();
        
        //add everything acl
        $element = new Varien_Simplexml_Element('<content_everything />');
        $element->addChild('title', 'Content Manager - All content types / All store views');
        $element->addChild('sort_order', 9);
        $element->addAttribute("aclpath", 'admin/contentmanager/content_everything');
        $element->addAttribute("module_c", 'contentmanager');
        $result->admin->children->cms->children->appendChild($element);  
        
        //loop content type
        foreach($collection as $contentType)
        {
            $element = new Varien_Simplexml_Element('<content_'.$contentType->getIdentifier().' />');
            $element->addChild('title', 'Content Manager - '.$contentType->getTitle());
            $element->addChild('sort_order', 10);
            $element->addAttribute("aclpath", 'admin/contentmanager/content_'.$contentType->getIdentifier());
            $element->addAttribute("module_c", 'contentmanager');
            
            
            
            $elementChildren = new Varien_Simplexml_Element('<children />');
            $elementStoreView = new Varien_Simplexml_Element('<content_'.$contentType->getIdentifier().'_view_0 />');
            $elementStoreView->addChild('title', Mage::helper('contentmanager')->__('All').' - '.Mage::helper('contentmanager')->__('View'));
            $elementStoreView->addChild('sort_order', 1);
            $elementStoreView->addAttribute("aclpath", 'admin/contentmanager/content_'.$contentType->getIdentifier().'_view_0');
            $elementStoreView->addAttribute("module_c", 'contentmanager');


            $elementStoreEdit = new Varien_Simplexml_Element('<content_'.$contentType->getIdentifier().'_edit_0 />');
            $elementStoreEdit->addChild('title', Mage::helper('contentmanager')->__('All').' - '.Mage::helper('contentmanager')->__('Edit'));
            $elementStoreEdit->addChild('sort_order', 1);
            $elementStoreEdit->addAttribute("aclpath", 'admin/contentmanager/content_'.$contentType->getIdentifier().'_edit_0');
            $elementStoreEdit->addAttribute("module_c", 'contentmanager');

            $elementChildren->appendChild($elementStoreView);
            $elementChildren->appendChild($elementStoreEdit);
                
            //loop store views
            $sortOrder = 2;
            foreach($stores as $store)
            {
                $elementStoreView = new Varien_Simplexml_Element('<content_'.$contentType->getIdentifier().'_view_'.$store->getId().' />');
                $elementStoreView->addChild('title', $store->getName().' - '.Mage::helper('contentmanager')->__('View'));
                $elementStoreView->addChild('sort_order', $sortOrder);
                $elementStoreView->addAttribute("aclpath", 'admin/contentmanager/content_'.$contentType->getIdentifier().'_view_'.$store->getId());
                $elementStoreView->addAttribute("module_c", 'contentmanager');
                
                
                $elementStoreEdit = new Varien_Simplexml_Element('<content_'.$contentType->getIdentifier().'_edit_'.$store->getId().' />');
                $elementStoreEdit->addChild('title', $store->getName().' - '.Mage::helper('contentmanager')->__('Edit'));
                $elementStoreEdit->addChild('sort_order', $sortOrder);
                $elementStoreEdit->addAttribute("aclpath", 'admin/contentmanager/content_'.$contentType->getIdentifier().'_edit_'.$store->getId());
                $elementStoreEdit->addAttribute("module_c", 'contentmanager');
                
                $elementChildren->appendChild($elementStoreView);
                $elementChildren->appendChild($elementStoreEdit);
                
                $sortOrder++;
            }
            $element->appendChild($elementChildren);
            
            $result->admin->children->cms->children->appendChild($element);
        }
        
        //menu permissions
        $elementStoreMenu = new Varien_Simplexml_Element('<manage_menu_0 />');
        $elementStoreMenu->addChild('title', Mage::helper('contentmanager')->__('All store views'));
        $elementStoreMenu->addChild('sort_order', 1);
        $elementStoreMenu->addAttribute("aclpath", 'admin/contentmanager/manage_menu_0');
        $elementStoreMenu->addAttribute("module_c", 'contentmanager');
        $elementChildren = new Varien_Simplexml_Element('<children />');
        $elementChildren->appendChild($elementStoreMenu);  
        $result->admin->children->contentmanager->children->manage_menu->appendChild($elementChildren);
        
        $sortOrder = 2;
        foreach($stores as $store)
        {
            $elementStoreMenu = new Varien_Simplexml_Element('<manage_menu_'.$store->getId().' />');
            $elementStoreMenu->addChild('title', $store->getName());
            $elementStoreMenu->addChild('sort_order', $sortOrder);
            $elementStoreMenu->addAttribute("aclpath", 'admin/contentmanager/manage_menu_'.$store->getId());
            $elementStoreMenu->addAttribute("module_c", 'contentmanager');
            
            $result->admin->children->contentmanager->children->manage_menu->children->appendChild($elementStoreMenu);

            $sortOrder++;
        }        
    }
    
}
