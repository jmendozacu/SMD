<?php

/**
 * RFQ line editable text field renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Qty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        $index = $this->getColumn()->getIndex();
        if ($row->getData($index)) {
            $value = $row->getData($index) * 1;
        } else {
            $value = $row->getData($index);
        }
        $productId = Mage::getModel('catalog/product')->getIdBySku($row->getData('product_code'));
        $decimalPlaces = 0;
        if ($productId) {
            $decimalPlaces = Mage::helper('epicor_comm')->getDecimalPlaces(Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'decimal_places', Mage::app()->getStore()->getStoreId()));
        }
        if (Mage::registry('rfqs_editable')) {
            $html = '<input decimal="' . $decimalPlaces . '" type="text" name="lines[' . $key . '][' . $row->getUniqueId() . '][' . $index . ']" value="' . $value . '" class="qty lines_' . $index . '"/>';
        } else {
            $html = $value;
            $html .= '<input decimal="' . $decimalPlaces . '" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][' . $index . ']" value="' . $value . '" class="qty lines_' . $index . '"/>';
        }

        return $html;
    }

}
