<?php

/**
 * Filesize grid column renderer. renders a file size in human readable format
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Filesize extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render country grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {
        $data = $row->getData($this->getColumn()->getIndex());

        return ($data > 0) ? Mage_System_Ftp::byteconvert($data) : '0';
    }

}
