<?php

/**
 * Locations Manufacturers renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Widget_Grid_Column_Renderer_Product_Locations_Manufacturers extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render country grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $manufacturers = $row->getData($this->getColumn()->getIndex());
        $html = '';
        if (!empty($manufacturers)) {
            $manufacturers = unserialize($manufacturers);
            if (!empty($manufacturers)) {
                foreach ($manufacturers as $manufacturer) {
                    if ($manufacturer['primary'] == 'Y') {
                        $html .= '<strong>';
                    }
                    
                    $html .= $manufacturer['name'];
                    
                    if (!empty($manufacturer['product_code'])) {
                        $html .= ' | ' . $this->__('SKU') . ': ' . $manufacturer['product_code'];
                    }
                    
                    if ($manufacturer['primary'] == 'Y') {
                        $html .= '</strong>';
                    }
                    
                    $html .= '<br />';
                }
            }
        }

        return $html;
    }

}
