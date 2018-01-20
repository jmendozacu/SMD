<?php

/**
 * CRQ line currency column renderer
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Crqs_Details_Lines_Renderer_Currency extends Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Currency
{

    public function render(Varien_Object $row)
    {
        $salesRepHelper = Mage::helper('epicor_salesrep');
        /* @var $salesRepHelper Epicor_SalesRep_Helper_Data */
        
        $pricingRuleProductHelper = Mage::helper('epicor_salesrep/pricing_rule_product');
        /* @var $pricingRuleProductHelper Epicor_SalesRep_Helper_Pricing_Rule_Product */
        
        $rowProduct = $row->getProduct();
        /* @var $rowProduct Epicor_Comm_Model_Product */
        
        if ($salesRepHelper->isEnabled() && Mage::registry('rfqs_editable') && !$rowProduct->isObjectNew() && $pricingRuleProductHelper->hasActiveRules()) {
            $key = Mage::registry('rfq_new') ? 'new' : 'existing';
            $rfq = Mage::registry('customer_connect_rfq_details');
            $index = $this->getColumn()->getIndex();
            
            $helper = Mage::helper('epicor_comm/messaging');
            /* @var $helper Epicor_Comm_Helper_Messaging */
            
            $currency = $helper->getCurrencyMapping($rfq->getCurrencyCode(), Epicor_Comm_Helper_Messaging::ERP_TO_MAGENTO);
            $currencySymbol = $helper->getCurrencySymbol($currency);
            $price = number_format($row->getData($index), 2, '.', '');
            $uniqueId = $row->getUniqueId();
            
            $basePrice = $pricingRuleProductHelper->getBasePrice($rowProduct, $row->getData('quantity'));
            $rulePrice = $pricingRuleProductHelper->getRuleBasePrice($rowProduct, $basePrice, $row->getData('quantity'));
            $minPrice = $pricingRuleProductHelper->getMinPrice($rowProduct, $basePrice);
            $maxDiscount = $pricingRuleProductHelper->getMaxDiscount($rowProduct, $basePrice);
            $discountPercent = $pricingRuleProductHelper->getDiscountAmount($price, $rulePrice);
            
            if ($basePrice > 0 && $rulePrice > 0 && $rulePrice > $minPrice) {
                
                $resetStyle = ($basePrice == $price) ? 'style="display:none"' : '';
                $resetLink = '<div id="reset_discount_' . $uniqueId . '" ' . $resetStyle . ' ><a href="javascript:resetDiscount(\'' . $uniqueId . '\')">' . $this->__('Revert to Web Price') . '</a></div>';

                $html = '
                    <div class="salesrep-discount-container" id="cart-item-' . $uniqueId . '">' 
                        . $currencySymbol
                        . '<input type="text" salesrep-cartid="' . $uniqueId . '" salesrep-type="price" name="lines[' . $key . '][' . $uniqueId . '][' . $index . ']" min-value="' . $minPrice . '" base-value="' . $rulePrice . '" orig-value="' . $price . '" web-price-value="' . $basePrice . '" value="' . $price . '" size="12" title="' . $this->__('Price') . '" class="input-text price lines_price no_update disabled" maxlength="20" />'
                        . '<input class="sr_base_price" type="hidden" value="' . $basePrice . '" name="lines[' . $key . '][' . $uniqueId . '][sr_base_price]"/>'                         
                        . '<p>'.$this->__('Discount') . '<input type="text" salesrep-cartid="' . $uniqueId . '" salesrep-type="discount" name="lines[' . $key . '][' . $uniqueId . '][discount]" max-value="' . $maxDiscount . '" orig-value="' . $discountPercent . '" value="' . $discountPercent . '" size="4" title="' . $this->__('Discount') . '" class="input-text discount disabled" maxlength="12" />%</p>'
                        . $resetLink  
                        . '<span class="lines_price_display" style="display:none"></span>
                </div>
';
            } else {
                $html = parent::render($row);
            }
        } else {
            $html = parent::render($row);
        }

        return $html;
    }

}
