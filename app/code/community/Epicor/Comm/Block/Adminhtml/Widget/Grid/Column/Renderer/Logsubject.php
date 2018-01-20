<?php

/**
 * Log subject renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Widget_Grid_Column_Renderer_Logsubject extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render country grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {
        $data = $row->getData($this->getColumn()->getIndex());
        return nl2br($data);
    }

}
