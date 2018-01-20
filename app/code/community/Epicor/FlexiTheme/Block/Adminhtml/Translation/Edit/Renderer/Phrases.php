<?php 
class Epicor_Flexitheme_Block_Adminhtml_Translation_Edit_Renderer_Phrases
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    protected $updateList = Array();
    public function render(Varien_Object $row)
    { 
        $relName = "translatedPhrase_".$row->getId();
        $phrases = $this->getColumn()->getPhrases();          
        $value = (!is_null($phrases->getData($relName))) ? $phrases->getData($relName) : $row->getData($this->getColumn()->getIndex());
        $html = '<input type="text" ';
        $html .= 'name="' . $this->getColumn()->getId() . '" ';
        $html .= 'rel="'.$relName. '" ';
        $html .= 'value="' . htmlentities($value). '"';     
        $html .= 'class="input-text ' . $this->getColumn()->getInlineCss() . '"/>';
        return $html;
    
    }
 
}
