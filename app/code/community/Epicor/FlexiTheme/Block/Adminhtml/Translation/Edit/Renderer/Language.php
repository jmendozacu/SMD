<?php 
class Epicor_Flexitheme_Block_Adminhtml_Translation_Edit_Renderer_Language
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    protected $updateList = Array();
    public function render(Varien_Object $row)
    { 
        $language = Mage::registry('translation_language_data');
        $row->setLanguageId($language['id']);                // will set same value for every row 
    }
 
}
