<?php

/**
 * Locations Manufacturers renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Widget_Grid_Column_Renderer_Product_Locations_Leadtime extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render country grid column
     *
     * @param   Epicor_Comm_Model_Location_Product $row
     * 
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $html = '';
        
        $days = $row->getLeadTimeDays();
        $text = $row->getLeadTimeText();
        
        if (!is_null($days)) {
            $html .= '<strong>' . $this->__('Days') . ':</strong> <span class="col-lead_time_days">' . $days . '</span><br />';
        }
        
        if (!is_null($text)) {
            $html .= '<strong>' . $this->__('Text') . ':</strong> <span class="col-lead_time_text">' . $text. '</span>';
        }

        return $html;
    }

}
