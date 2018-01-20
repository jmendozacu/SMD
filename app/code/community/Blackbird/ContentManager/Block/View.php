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

class Blackbird_ContentManager_Block_View extends Mage_Catalog_Block_Product_Abstract
{
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $content = Mage::registry('current_content');
        $ct = Mage::registry('current_ct');
        
        //Applied content layout in cascading
        
        if($ct->getLayout() == 0)
        {
            //test applying view-ID.phtml
            $this->setTemplate('contenttype/view-'.$content->getId().'.phtml');
            if(!file_exists(Mage::getBaseDir('app') . DS . 'design' . DS . $this->getTemplateFile()))
            {
                //test applying view-type.phtml
                $this->setTemplate('contenttype/view-'.$ct->getIdentifier().'.phtml');
                if(!file_exists(Mage::getBaseDir('app') . DS . 'design' . DS . $this->getTemplateFile()))
                {
                    //applying default view.phtml
                    $this->setTemplate('contenttype/view.phtml');
                }
            }
        }
        else
        {
            //test applying view/layout-ID.phtml
            $this->setTemplate('contenttype/view/layout-'.$ct->getLayout().'.phtml');
            if(!file_exists(Mage::getBaseDir('app') . DS . 'design' . DS . $this->getTemplateFile()))
            {
                //applying default view.phtml
                $this->setTemplate('contenttype/view.phtml');
            }
        }
    }
    
    public function getContent()
    {
        return Mage::registry('current_content');
    }
    
    public function getLayoutItems()
    {
        $masterCollection = array();
        $contentType = Mage::registry('current_ct');
        
        //GROUPS
        $collection = Mage::getModel('contentmanager/contenttype_layout_group')
                ->getCollection()
                ->addFieldToFilter('parent_layout_group_id', array('null' => true))
                ->addFieldToFilter('ct_id', $contentType->getId());
        
        $collection->getSelect()->order('sort_order');

        foreach($collection as $layoutItem)
        {
            $this->loadLayoutChildItems($layoutItem);
            
            $masterCollection[$layoutItem->getColumn()][$layoutItem->getSortOrder()] = $layoutItem;
        }
        
        //FIELDS
        $collection = Mage::getModel('contentmanager/contenttype_layout_field')
                ->getCollection()
                ->addFieldToFilter('layout_group_id', array('null' => true))
                ->addFieldToFilter('ct_id', $contentType->getId());
        
        $collection->getSelect()->order('sort_order');

        foreach($collection as $layoutItem)
        {
            $masterCollection[$layoutItem->getColumn()][$layoutItem->getSortOrder()] = $layoutItem;
        }

        //BLOCKS
        $collection = Mage::getModel('contentmanager/contenttype_layout_block')
                ->getCollection()
                ->addFieldToFilter('layout_group_id', array('null' => true))
                ->addFieldToFilter('ct_id', $contentType->getId());
        
        $collection->getSelect()->order('sort_order');        
        
        foreach($collection as $layoutItem)
        {
            $masterCollection[$layoutItem->getColumn()][$layoutItem->getSortOrder()] = $layoutItem;
        }
        
        //Order by column and sort order
        ksort($masterCollection);
        foreach($masterCollection as $columnId => $columnCollection)
        {
            ksort($columnCollection);
            $masterCollection[$columnId] = $columnCollection;
        }
        
        return $masterCollection;
    }
    
    public function loadLayoutChildItems($layoutGroup)
    {
        $masterCollection = array();
        $contentType = Mage::registry('current_ct');
        
        //GROUPS
        $collection = Mage::getModel('contentmanager/contenttype_layout_group')
                ->getCollection()
                ->addFieldToFilter('parent_layout_group_id', array('eq' => $layoutGroup->getId()))
                ->addFieldToFilter('ct_id', $contentType->getId());
        
        $collection->getSelect()->order('sort_order');

        foreach($collection as $layoutItem)
        {
            $this->loadLayoutChildItems($layoutItem);
            
            $masterCollection[$layoutItem->getSortOrder()] = $layoutItem;
        }
        
        //FIELDS
        $collection = Mage::getModel('contentmanager/contenttype_layout_field')
                ->getCollection()
                ->addFieldToFilter('layout_group_id', array('eq' => $layoutGroup->getId()))
                ->addFieldToFilter('ct_id', $contentType->getId());
        
        $collection->getSelect()->order('sort_order');

        foreach($collection as $layoutItem)
        {
            $masterCollection[$layoutItem->getSortOrder()] = $layoutItem;
        }

        //BLOCKS
        $collection = Mage::getModel('contentmanager/contenttype_layout_block')
                ->getCollection()
                ->addFieldToFilter('layout_group_id', array('eq' => $layoutGroup->getId()))
                ->addFieldToFilter('ct_id', $contentType->getId());
        
        $collection->getSelect()->order('sort_order');        
        
        foreach($collection as $layoutItem)
        {
            $masterCollection[$layoutItem->getSortOrder()] = $layoutItem;
        }
        
        //Order by column and sort order
        ksort($masterCollection);
        
        $layoutGroup->setChilds($masterCollection);
    }
    
}