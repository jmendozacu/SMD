<?php

/**
 * Price Renderer for Line Contract Select Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Cart_Contract_Select_Renderer_Price extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render column
     *
     * @param   Epicor_Lists_Model_List $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $store = Mage::app()->getStore();
        /* @var $store Epicor_Comm_Model_Store */
        $html = $store->formatPrice($row->getPrice());

        if ($row->getPriceBreaks()) {
            $html .= '<br / ><ul class="tier-prices product-pricing">';

            foreach ($row->getPriceBreaks() as $x => $break) {
                $discountVal = round(100 - (($break->getPrice() / $row->getPrice()) * 100));
                $discount = '<span class="percent tier-' . $x . '">' . $discountVal . '%</span>';
                $saving = '<strong class="benefit">' . $this->__('save %s', $discount) . '</strong>';
                $priceDisplay = '<span class="price">' . $store->formatPrice($break->getPrice(), false) . '</span>';
                $html .= '<li class="tier-price tier-' . $x . '">';
                $html .= $this->__('Buy %s for %s each and %s', $break->getQuantity(), $priceDisplay, $saving);
                $html .= '</li>';
            }

            $html .= '</ul>';
        }
        return $html;
    }

}
