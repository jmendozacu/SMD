<?php

class Epicor_FlexiTheme_Model_Mysql4_Layout_Page_Block_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('flexitheme/layout_page_block');
    }

    public function toSectionsArray()
    {
        $sections_array = array();
        $this->load();
        /* @var $sections Epicor_FlexiTheme_Model_Mysql4_Layout_Template_Section_Collection */
        $sections = Mage::getModel('flexitheme/layout_template_section')->getCollection();
        $blockData = Mage::getModel('flexitheme/layout_block')->getCollection()->getItems();
        
        $blocks = array();
        foreach ($blockData as $block) {
            $blocks[$block->getBlockName()] = $block;
        }

        foreach ($this->getItems() as $layoutPageBlock) {
            $sections_array[$sections->getItemById($layoutPageBlock->getSectionId())->getSectionName()][] = $blocks[$layoutPageBlock->getBlockName()];
        }

        return $sections_array;
    }

    public function toSectionsXmlArray($id_tag = '')
    {
        $sections = $this->toSectionsArray();
        $sections_xml = array();
        foreach ($sections as $section => $blocks) {
            $layout_xml = '';
            foreach ($blocks as $block) {
                if ($block->getBlockType()) {
                    $block_template = $block->getTemplate($section);
                    $layout_xml.= '
            <block type="' . $block->getBlockType() . '" name="flexitheme.' . $id_tag . $section . '.' . $block->getBlockXmlName() . '" ' . (empty($block_template) ? '' : 'template="' . $block_template . '"');
                    $block_xml = $block->getBlockXml();
                    if (empty($block_xml))
                        $layout_xml.= ' />';
                    else
                        $layout_xml.= ">\n" . $block->getBlockXml() . "\n </block>";
                } else {
                    $layout_xml .= '
            ' . $block->getBlockXml();
                }
            }
            $sections_xml[$section] = $layout_xml;
        }
        return $sections_xml;
    }

}