<?php

class Blackbird_ContentManager_Block_Adminhtml_Content_Renderer_Template extends Mage_Adminhtml_Block_Abstract
{
    public function setRendererTemplate($renderer)
    {
        $this->setTemplate('blackbird/contentmanager/grid/'.$renderer.'.phtml');
    }
    
    public function createButton($label, $url, $classes = "")
    {
        return '<button title="'.$label.'" type="button" class="scalable '.$classes.'" onclick="setLocation(\''.$url.'\')" style=""><span><span><span>'.$label.'</span></span></span></button>';
    }
    
    public function isAllowed($storeId)
    {
        $contentTypeModel = Mage::registry('current_contenttype');
        if(!$contentTypeModel)
        {
            $content = $this->getContentModel(0, $this->getRowId());
            $contentTypeId = $content->getCtId();
        }
        else
        {
            $contentTypeId = $contentTypeModel->getIdentifier();
        }
        
        return Mage::getSingleton('admin/session')->isAllowed('contentmanager/content_'.$contentTypeId.'_view_0') || Mage::getSingleton('admin/session')->isAllowed('contentmanager/content_'.$contentTypeId.'_view_'.$storeId) || Mage::getSingleton('admin/session')->isAllowed('contentmanager/content_everything');
    }      
}

