<?php

/**
 * Column Renderer for Shipto Select Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Contract_Shipto_Grid_Renderer_Select extends Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Action
{

    public function render(Varien_Object $row)
    {
        $actions = $this->getColumn()->getActions();
        if (empty($actions) || !is_array($actions)) {
            return '&nbsp;';
        }
        if ($this->getColumn()->getLinks() == true) {
            $contractHelper = Mage::helper('epicor_lists/frontend_contract');
            /* @var $contractHelper Epicor_Lists_Helper_Frontend_Contract */
            $shipto = $contractHelper->getSelectedContractShipto();

            if ($row->getErpAddressCode() === $shipto) {
                $html = $this->__('Currently Selected');
                return $html;
            }
        }

        return parent::render($row);
    }

}
