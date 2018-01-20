<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Type
 *
 * @author Paul.Ketelle
 */
class Epicor_FlexiTheme_Block_Adminhtml_Renderer_Layout_Block_Callout_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $data = unserialize($row->getData($this->getColumn()->getIndex()));
        $row_type = $data['type'];
        $types = Mage::getModel('flexitheme/config_source_callouttypes')->toOptionArray();
        foreach($types as $type){
            $temp .= '<span>'.$type['value'].' = > '.$row_type.'</span>';
            if($type['value'] == $row_type)
                return $type['label'];
        }
        return $temp;
    }

}


