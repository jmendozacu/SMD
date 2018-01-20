<?php

/**
 * Active column renderer
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_Widget_Grid_Column_Renderer_Analyse_Products extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render product grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $isListExclude = $row->hasSetting('E');
        $listProducts = array_keys($row->getProducts());
        $accumulatedProducts = Mage::registry('epicor_lists_analyse_accumulated_products') ? : array();
        $accumulateType = Mage::registry('epicor_lists_analyse_accumulated_products_type');
        $sku = $this->getColumn()->getSku();

        if ($accumulateType == 'E') {
            $resultProducts = $isListExclude ? array_merge($accumulatedProducts, $listProducts) : array_diff($listProducts, $accumulatedProducts);
            $accumulateType = $isListExclude ? 'E' : 'I';
        } elseif ($accumulateType == 'I') {
            $resultProducts = $isListExclude ? array_diff($accumulatedProducts, $listProducts) : array_intersect($accumulatedProducts, $listProducts);
        } else {
            $resultProducts = $listProducts;
            $accumulateType = $isListExclude ? 'E' : 'I';
        }
        Mage::unregister('epicor_lists_analyse_accumulated_products');
        Mage::unregister('epicor_lists_analyse_accumulated_products_type');
        Mage::register('epicor_lists_analyse_accumulated_products', $resultProducts);
        Mage::register('epicor_lists_analyse_accumulated_products_type', $accumulateType);

        if ($sku) {
            if ((in_array($sku, $resultProducts) && $accumulateType == 'I') || (!in_array($sku, $resultProducts) && $accumulateType == 'E')) {
                $html = $this->__('Available');
            } else {
                $html = $this->__('Not Available');
            }
        } else {
            $html = "Filtered List Total: " . count($resultProducts);
        }

        $listCount = count($listProducts);

        $html.= " <br/>  Total Products on List: " . $listCount;
        $html.= '<input type="hidden" class="filteredrow" value=\'' . count($resultProducts) . '\' />';
        $data = array(
            'list_id' => $row->getId(),
            'products' => $resultProducts,
            'type' => $accumulateType
        );

        $html .= '<input type="hidden" name="data" class="data" value=\'' . base64_encode(json_encode($data)) . '\' />';

        return $html;
    }

}
