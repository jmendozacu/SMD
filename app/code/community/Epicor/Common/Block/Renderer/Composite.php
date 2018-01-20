<?php

/**
 * Part number display on invoice details
 *
 * @author Gareth.James
 */
class Epicor_Common_Block_Renderer_Composite extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        
        $keys = $this->getColumn()->getKeys();
        $labels = $this->getColumn()->getLabels();

        $html = '';
        $join = '';
        foreach($keys as $key) {
            $html .= $join;
            $html .= isset($labels[$key]) ? '<strong>'.$labels[$key] . ': </strong>' : '';
            $html .= is_numeric($row->getData($key))? floatval($row->getData($key)) : $row->getData($key);   // remove traling 0s from a numeric field
            $join = $this->getColumn()->getJoin();
        }
        
        return  $html;
    }

}
