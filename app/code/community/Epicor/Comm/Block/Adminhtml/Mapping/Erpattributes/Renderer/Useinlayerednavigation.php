<?php

/**
 * display actual labels for use in layered navigation column in epicor_comm/erp_mapping_attributes
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Mapping_Erpattributes_Renderer_Useinlayerednavigation extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $options = array(
            array('value' => '0', 'label' => Mage::helper('catalog')->__('No')),
            array('value' => '1', 'label' => Mage::helper('catalog')->__('Filterable (with results)')),
            array('value' => '2', 'label' => Mage::helper('catalog')->__('Filterable (no results)')),
        );
        return $options[$row->getData($this->getColumn()->getIndex())]['label'];
    
    }  

}
