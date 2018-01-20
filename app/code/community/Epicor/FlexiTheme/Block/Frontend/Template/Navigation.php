<?php
class Epicor_FlexiTheme_Block_Frontend_Template_Navigation extends Mage_Page_Block_Template_Links
{
    
    public function setBlockId($block_id) {
        
        $block = Mage::getModel('flexitheme/layout_block')->load(base64_decode($block_id), 'block_name');
        $data = unserialize($block->getBlockExtra());
        $this->setName($data['identifier']);
        foreach($data['links'] as $link_id) {
            if($link_id > 0) {
             //Custom Link  
                $link = Mage::getModel('flexitheme/layout_block_link')->load($link_id);
                if($link->getId())
                    $this->addLink($link->getDisplayTitle(), 
                            $link->getLinkUrl(), 
                            $link->getTooltipTitle(), 
                            false, 
                            array(), 
                            $data['order'][$link_id],
                            array('class'=> $link->getLinkIdentifier()));
            } else {
             //CMS Page Link
                
                $cms_page = Mage::getModel('cms/page')->load($link_id * -1);
                if($cms_page->getId())
                    
                    $this->addLink($cms_page->getTitle(), 
                            '/'.$cms_page->getIdentifier(), 
                            $cms_page->getTitle(), 
                            false, 
                            array(), 
                            $data['order'][$link_id],
                            array('class'=> $cms_page->getIdentifier()));
            }
        }
    }
}


