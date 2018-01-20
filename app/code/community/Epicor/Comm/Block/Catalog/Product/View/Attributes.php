<?php

class Epicor_Comm_Block_Catalog_Product_View_Attributes extends Mage_Catalog_Block_Product_View_Attributes {

    public function getAdditionalData(array $excludeAttr = array())
    {
        $data = array();
        $product = $this->getProduct();
        $attributes = $product->getAttributes();
        $truncateTrailingZeros = Mage::getStoreConfigFlag('epicor_product_config/weights/truncate_trailing_zeros');
        $weightDecimalPrecision = Mage::getStoreConfig('epicor_product_config/weights/weight_decimal_precision');
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if ($attribute->getIsVisibleOnFront() && !in_array($attributeCode, $excludeAttr)) {
                $value = $attribute->getFrontend()->getValue($product);

                if (!$product->hasData($attributeCode) || (string)$value == '') {
                    $value = '';
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = Mage::app()->getStore()->convertPrice($value, true);
                }

                if (is_string($value) && strlen($value)) {
                    if ($attributeCode == 'weight') {
                        if (!empty($weightDecimalPrecision)) {
                            $value = Zend_Locale_Math::round($value, $weightDecimalPrecision);
                        }
                        if ($truncateTrailingZeros) {
                            $value = floatval($value);
                        }
                    }
                    $data[$attributeCode] = array(
                        'label' => $attribute->getStoreLabel(),
                        'value' => $value,
                        'code'  => $attributeCode
                    );
                }
            }
        }
        return $data;
    }
}
