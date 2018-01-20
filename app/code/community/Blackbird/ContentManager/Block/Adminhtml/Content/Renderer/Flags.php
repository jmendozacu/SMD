<?php

class Blackbird_ContentManager_Block_Adminhtml_Content_Renderer_Flags extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $content = $row->getData();
        $entity_id = $content['entity_id'];
        
        $block = $this->getLayout()->createBlock('contentmanager/adminhtml_content_renderer_template');
        $block->setRendererTemplate('flags');
        $block->setEntityId($entity_id);
        $block->setRowId($row->getId());
        $block->setColumn($this->getColumn());
        
        return $block->toHtml();
    }
    
}

