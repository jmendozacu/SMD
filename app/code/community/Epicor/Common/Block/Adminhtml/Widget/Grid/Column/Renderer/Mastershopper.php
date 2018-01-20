<?php

/**
 * List admin actions
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Mastershopper extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render master shopper column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $tick = ' <img src="' . $this->getSkinUrl('epicor/common/images/success_msg_icon.gif') . '" alt="Yes" /> ';
        $cross = ' <img src="' . $this->getSkinUrl('epicor/common/images/cancel_icon.gif') . '" alt="No" /> ';

        switch ($row->getMasterShopper()) {
            case 'y':
                $display = $tick;
                break;
            case 'n':
                $display = $cross;
                break;
            
        }

        return $display;
    }

}
