<?php

class Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Tickcross extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render country grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $data = $row->getData($this->getColumn()->getIndex());

        $mode = $this->getColumn()->getTickMode() ? : 'boolean';

        $displayTick = false;

        if ($mode == 'empty') {
            if (empty($data)) {
                $displayTick = true;
            }
        } else if ($mode == 'content') {
            if (!empty($data)) {
                $displayTick = true;
            }
        } else {
            $displayTick = filter_var($data, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }
        $output = '';
        if ($displayTick) {
            $output = ' <img src="' . $this->getSkinUrl('epicor/common/images/success_msg_icon.gif') . '" alt="Yes" /> ';
        } else {  
            if($row->getSource() != 1){     // don't display cross if ECC only
                $output = ' <img src="' . $this->getSkinUrl('epicor/common/images/cancel_icon.gif') . '" alt="No" /> ';
            }    
        }

        return $output;
    }

}
