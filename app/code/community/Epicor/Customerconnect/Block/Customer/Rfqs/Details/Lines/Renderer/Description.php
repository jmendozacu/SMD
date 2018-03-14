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
        if (Mage::registry('rfqs_editable') && !in_array($type, array('S', 'X'))) {
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

            // This gets the quote sort order from admin and reorders the ewa fields accordingly
            $attributeData = array();
            $ewaAttributes = array('ewaTitle' => 'ewa_title', 'ewaSku' => 'ewa_sku', 'ewaShortDescription' => 'ewa_short_description', 'ewaDescription' => 'ewa_description');
            foreach ($attributes as $attribute) {
                $attributeData[$attribute['description']] = $attribute['value'];
            }
            $newOptionsOrder = Mage::getStoreConfig('Epicor_Comm/ewa_options/quote_display_fields');
            $newoptionsOrder = $newOptionsOrder ? unserialize($newOptionsOrder) : null;

            $newOptionByTypeOrder = array();

            foreach ($newoptionsOrder as $key => $option) {
                //               $newOptionByTypeOrder[] = array($key=>$option) ;
                $newOptionByTypeOrder[$option['ewaquotesortorder']] = array($key => $option);
            }
            $requiredEwaOptions = array_intersect_key($newOptionByTypeOrder, array_flip($ewaAttributes));
            $ewaAttributes = array_flip(array_replace($requiredEwaOptions, array_flip($ewaAttributes)));
            /* @var $product Epicor_Comm_Model_Product */
            foreach ($ewaAttributes as $key => $ewaAttribute) {
                if (isset($attributeData[$key])) {
                    if (Mage::registry('rfqs_editable')) {
                        $setBase64 = ($key =="ewaSku") ? $attributeData[$key] :base64_encode($attributeData[$key]);
                        $row->setData($ewaAttribute, $setBase64);
                        $product->setData($ewaAttribute, $setBase64);
                    }
                    if ($helper->getEwaDisplay($ewaAttribute)) {
                        $description[] = $attributeData[$key];
                    }
                }
            }
        }

        return $html . '<span class="description_display">' . join('<br /><br />', $description) . '</span>';
    }

}
