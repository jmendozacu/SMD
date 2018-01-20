<?php

/**
 * Syn log types renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Message_Syn_Log_Renderer_Types
        extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $data = $row->getData($this->getColumn()->getIndex());

        if (!empty($data)) {
            $html = implode(', ',unserialize($data));
        } else {
            $html = '';
        }
        
        $helper = Mage::helper('epicor_comm/entityreg');
        /* @var $helper Epicor_Comm_Helper_Entityreg */
        
        $typeDescs = $helper->getRegistryTypeDescriptions(unserialize($data));
        
        $search = array_keys($typeDescs);
        $replace = array_values($typeDescs);
        
        $html = str_replace($search, $replace, $html);

        return $html;
    }

}
