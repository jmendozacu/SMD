<?php

/**
 * RFQ line delete column renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Select extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */

        $atts = array();
        
        if (Mage::registry('rfqs_editable')) {
            $html = '<input type="checkbox" class="lines_select" name="lines[' . $key . '][' . $row->getUniqueId() . '][select]" />';

            $atts = $this->_getRowAttributes($row);
            $productJson = $this->_getRowProductJson($row);

            $html .= '<input class="lines_product_json" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][lines_product_json]" value="' . $productJson . '" /> ';
            $html .= '<input class="lines_product_id" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][lines_product_id]" value="' . $row->getProduct()->getId() . '" /> ';
            $html .= '<input class="lines_child_id" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][lines_child_id]" value="" /> ';
        } else {
            $html = '';
        }

        $html .= '<input type="hidden" class="lines_delete" name="lines[' . $key . '][' . $row->getUniqueId() . '][delete]" />';

        $oldDetails = base64_encode(serialize($helper->varienToArray($row)));

        if (!Mage::registry('rfq_new')) {
            $html .= '<input type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][old_data]" value="' . $oldDetails . '" /> ';
        }

        $html .= '<input class="lines_configured" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][configured]" value="" /> ';
        $html .= '<input class="lines_tax_code" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][tax_code]" value="' . $row->getData('tax_code') . '" /> ';
        $html .= '<input class="lines_product_code" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][product_code]" value="' . $row->getData('product_code') . '" /> ';
        $html .= '<input class="lines_type" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][type]" value="' . $row->getData('product_code__attributes_type') . '" /> ';
        $html .= '<input class="lines_orig_quantity" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][origqty]" value="' . $row->getData('quantity') . '" /> ';
        $html .= '<input class="lines_sku" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][skuref]" value="' . $row->getData('product_code') . '" /> ';
        $html .= '<input class="lines_uom" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][uomref]" value="' . $row->getData('unit_of_measure_code') . '" /> ';
        $html .= '<input class="lines_unit_of_measure_code" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][unit_of_measure_code]" value="' . $row->getData('unit_of_measure_code') . '" /> ';
        $html .= '<input class="lines_group_sequence" id="group_sequence_' . $row->getUniqueId() . '" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][group_sequence]" value="' . $row->getData('group_sequence') . '" /> ';
        $html .= '<input class="lines_ewa_code" id="ewa_code_' . $row->getUniqueId() . '" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][ewa_code]" value="' . $row->getEwaCode() . '" /> ';

        $html .= '<input class="lines_ewa_title" id="ewa_title_' . $row->getUniqueId() . '" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][ewa_title]" value="' . $row->getEwaTitle() . '" /> ';
        $html .= '<input class="lines_ewa_sku" id="ewa_sku_' . $row->getUniqueId() . '" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][ewa_sku]" value="' . $row->getEwaSku() . '" /> ';
        $html .= '<input class="lines_ewa_short_description" id="ewa_short_description_' . $row->getUniqueId() . '" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][ewa_code]" value="' . $row->getEwaShortDescription() . '" /> ';
        $html .= '<input class="lines_ewa_description" id="ewa_ewa_description_' . $row->getUniqueId() . '" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][ewa_code]" value="' . $row->getEwaDescription() . '" /> ';

        $html .= '<input class="lines_attributes" type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][attributes]" value="' . htmlentities(base64_encode(serialize($atts))) . '" /> ';

        return $html;
    }

    protected function _getRowAttributes($row)
    {
        $groupSequence = $row->getData('group_sequence');
        $attributes = $row->getData('attributes');
        $atts = array();
        if ($attributes) {
            $attData = $attributes->getasarrayAttribute();

            foreach ($attData as $att) {
                if ($att->getDescription() == 'groupSequence') {
                    $groupSequence = $att->getValue();
                }
                $atts[] = array(
                    'description' => $att->getDescription(),
                    'value' => $att->getValue(),
                );
            }
        } else if (!empty($groupSequence)) {
            $atts[] = array(
                'description' => 'groupSequence',
                'value' => $groupSequence,
            );
        }
        
        $row->setGroupSequence($groupSequence);

        return $atts;
    }

    protected function _getRowProductJson($row)
    {
        $helper = Mage::helper('customerconnect/rfq');
        /* @var $helper Epicor_Customerconnect_Helper_Rfq */
        
        $rfq = Mage::registry('customer_connect_rfq_details');
        $currency = $helper->getCurrencyMapping($rfq->getCurrencyCode(), Epicor_Comm_Helper_Messaging::ERP_TO_MAGENTO);
        $rowPrice = $row->getPrice();
        $rowQty = $row->getQuantity();

        $productHelper = Mage::helper('epicor_comm/product');
        /* @var $productHelper Epicor_Comm_Helper_Product */

        $rowProduct = $row->getProduct();
        /* @var $rowProduct Epicor_Comm_Model_Product */

        if ($rowPrice == 'TBC') {
            $formattedPrice = $this->__('TBC');
            $formattedTotal = $this->__('TBC');
        } else {
            $formattedPrice = $helper->formatPrice($rowPrice, true, $currency);
            $formattedTotal = $helper->formatPrice($rowPrice * $rowQty, true, $currency);
        }

        $rowProduct->setIsCustom($rowProduct->isObjectNew());
        $rowProduct->setUsePrice($rowPrice);
        $rowProduct->setMsqFormattedPrice($formattedPrice);
        $rowProduct->setMsqFormattedTotal($formattedTotal);
        $rowProduct->setMsqQty($rowQty);
        
        $configurable = $helper->getAttributeValueFromLineByDescription($row , array('configurable')) ?: 'Y';
        $rowProduct->setEwaConfigurable($configurable);
        $productInfo = $productHelper->getProductInfoArray($rowProduct);
        $productInfo['request_date'] = $rowProduct->getRequestDate();

        return htmlentities(json_encode($productInfo));
    }

}
