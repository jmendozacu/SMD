<?php

class Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Rowdata
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    
    public function render(Varien_Object $row)
    { 
        $jsonArray = json_encode($row->getData());
        $html = '
            <input rel="'.$row->getId().'" id="row-'.$row->getId().'" name="rowData[]" type="hidden" value=\''.$jsonArray.'\' class="rowdata" />';
        return $html;      
    
    }
 
}