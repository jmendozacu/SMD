<?php

/**
 * Invoice Reorder link grid renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Renderer_Remotelinks
        extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('epicor_comm/remotelinks');
        /* @var $helper Epicor_Comm_Helper_Remotelinks */
        
        $remotelink_code = $this->getColumn()->getRemotelinkCode();
        $title = $this->getColumn()->getRemotelinkLabel();
        $url = $helper->fieldSubstitution($row, $remotelink_code);
        $html = '<a title="'.$title.'" href="'.$url.'">'.$title.'</a>';

        return $html;
    }

}
