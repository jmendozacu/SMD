<?php 
class Epicor_Flexitheme_Block_Adminhtml_Translation_Edit_Renderer_Rowdata
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    protected $updateList = Array();
    public function render(Varien_Object $row)
    { 
        $helper = Mage::helper('flexitheme');
        $rowArray['id'] = $row->getId();
        $rowArray['translation_string'] = $row->getTranslationString();
        $rowArray['translated_phrase'] = $row->getTranslatedPhrase();
        $rowArray['language_id'] = $row->getLanguageId();       
        $jsonArray = json_encode($rowArray);
        $row->setRowdata($helper->urlencode($jsonArray));
//        $html = '<input type="text" name="rowdata"';
//        $html .= 'value="' . htmlspecialchars($jsonArray) . '"/> ';
//        return $html;      
    
    }
 
}
