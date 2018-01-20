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
class Epicor_FlexiTheme_Block_Adminhtml_Renderer_Layout_Block_Navigation_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $values = $this->getColumn()->getValues();
        return '<input type="text" value="'.$values[$row->getId()].'" name="order['.$row->getId().']" />';
    }

}


