<?php

/**
 * Log url renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Widget_Grid_Column_Renderer_Logurl extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render country grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {
        $data = $row->getData($this->getColumn()->getIndex());
        
        $html = '';
        
        if(!empty($data)) {
            if(strpos($data,'comm/data') === false) {
                $html .= '<span style="display: block; white-space: nowrap; width: 200px; overflow-y: hidden;">'.$data.'</span>';
                $html .= '<a href="'.$data.'">Go to URL</a>';
            } else {
                $html .= 'System Url';
            }
        }
        
        return $html;
    }

}
