<?php

/**
 * RFQ line editable text field renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Description extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('epicor_comm/configurator');
        /* @var $helper Epicor_Comm_Helper_Configurator */
        
        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        $index = $this->getColumn()->getIndex();

        $type = $row->getData('product_code__attributes_type');
        
        $product = $row->getProduct();

        $description = array();
        $value = $row->getData($index) ? $row->getData($index) : $value;
        if (Mage::registry('rfqs_editable') && $type != 'S') {
            $html = '<input type="text" name="lines[' . $key . '][' . $row->getUniqueId() . '][' . $index . ']" value="' . $value . '" class="lines_' . $index . ' required-entry"/>';
        } else {
            if ($product && $product->getConfigurator()) {
                if($helper->getEwaDisplay('base_description')){
                    $description[] = $row->getDescription();
                }
            }else{
                $description[] = $row->getData($index);
            }
            $html = '<input type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][' . $index . ']" value="' . $value . '" class="lines_' . $index . '"/>';
        }

        if ($row->getAttributes()) {
            $attGroup = $row->getAttributes();
            $attributes = $attGroup->getasarrayAttribute();

            $attributeData = array();
            foreach ($attributes as $attribute) {
                $attributeData[$attribute['description']] = $attribute['value'];
            }
            
            /* @var $product Epicor_Comm_Model_Product */

            
            if (isset($attributeData['ewaTitle'])) {
                if (Mage::registry('rfqs_editable')) {
                    $row->setData('ewa_title', base64_encode($attributeData['ewaTitle']));
                    $product->setData('ewa_title', base64_encode($attributeData['ewaTitle']));
                }
                if ($helper->getEwaDisplay('ewa_title')) {
                    $description[] = $attributeData['ewaTitle'];
                }
            }

            if (isset($attributeData['ewaSku'])) {
                if (Mage::registry('rfqs_editable')) {
                    $row->setData('ewa_sku', $attributeData['ewaSku']);
                    $product->setData('ewa_sku', $attributeData['ewaSku']);
                }
                if ($helper->getEwaDisplay('ewa_sku')) {
                    $description[] = $attributeData['ewaSku'];
                }
            }

            if (isset($attributeData['ewaShortDescription'])) {
                if (Mage::registry('rfqs_editable')) {
                    $row->setData('ewa_short_description', base64_encode($attributeData['ewaShortDescription']));
                    $product->setData('ewa_short_description', base64_encode($attributeData['ewaShortDescription']));
                }
                if ($helper->getEwaDisplay('ewa_short_description')) {
                    $description[] = $attributeData['ewaShortDescription'];
                }
            }

            if (isset($attributeData['ewaDescription'])) {
                if (Mage::registry('rfqs_editable')) {
                    $row->setData('ewa_description', base64_encode($attributeData['ewaDescription']));
                    $product->setData('ewa_description', base64_encode($attributeData['ewaDescription']));
                }
                if ($helper->getEwaDisplay('ewa_description')) {
                    $description[] = $attributeData['ewaDescription'];
                }
            }
        }

        return '<span class="description_display">' . $html . join('<br /><br />', $description) . '</span>';
    }

}
