<?php

/**
 * Syn log link renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Message_Syn_Log_Renderer_Uploadedlink
        extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render column
     *
     * @param   Epicor_Comm_Model_Syn_Log $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Comm_Model_Syn_Log */
        $helper = Mage::helper('epicor_comm/entityreg');
        /* @var $helper Epicor_Comm_Helper_Entityreg */
        
        $typeFilter = implode(',', $helper->getRegistryTypeDescriptions(unserialize($row->getTypes())));
        
        if (!empty($typeFilter)) {
            $locale = Mage::app()->getLocale()->getLocaleCode();
        
            $from = $helper->urlEncode(Mage::getUrl('*/*/*', $this->getRequest()->getParams()));
            $modified = $helper->getLocalDate(
                strtotime($row->getCreatedAt()), 
                Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, 
                true
            );

            $link = Mage::helper('adminhtml')->getUrl(
                'adminhtml/epicorcomm_advanced_entityreg/index',
                array(
                'filter' => urlencode($helper->urlEncode('type=' . $typeFilter
                . '&is_dirty=1'
                . '&modified_at[locale]=' . $locale
                . '&modified_at[to]=' . $modified)),
                'back' => $from
                )
            );
            $html = '<a href="' . $link . '">' . $this->__('Purge List') . '</a>';
        } else {
            $html = '';
        }
        
        return $html;
    }
}
