<?php

/**
 * RFQ Part Number comments renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Partnumber extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('customerconnect/rfq');
        /* @var $helper Epicor_Customerconnect_Helper_Rfq */
        
        $index = $this->getColumn()->getIndex();
        $productCode = $this->htmlEscape($row->getData($index));
        $productCode = $helper->getAttributeValueFromLineByDescription($row , array('ewaSku', 'ewa_sku', 'ewa sku')) ?: $productCode;
        $configurable = $helper->getAttributeValueFromLineByDescription($row , array('configurable')) ?: 'Y';
        $html = $productCode;

        if (Mage::registry('rfqs_editable')) {
            if ($configurable == 'Y' && ($row->getGroupSequence() || $row->getEwaCode()) && $row->getProduct()->getConfigurator()) {
                $html .= '<br /><a href="javascript: configureEwaProduct(\'' . $row->getUniqueId() . '\')">';
                $html .= $this->__('Edit Configuration');
                $html .= '</a>';
            }
        }

        return '<span class="product_code_display">' . $html . '</span>';
    }

}
