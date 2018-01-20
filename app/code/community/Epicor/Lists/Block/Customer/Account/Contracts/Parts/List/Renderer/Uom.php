<?php

/**
 * RFQ line attachments column renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Customer_Account_Contracts_parts_List_Renderer_Uom extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('epicor_lists/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        $html = '';
        
        $colspan = 7;
        $productCode = $row->getProductCode();
        $uoms = $row->getUnitOfMeasures()->getasarrayUnitOfMeasure();
        $uomArray = array();
        if(!empty($uoms)){                      // sort uoms by code
            foreach($uoms as $uom){
                $uomArray[$uom->getUnitOfMeasureCode()] = $uom;
            }
            ksort($uomArray);
        }
        $id = 'parts_row_uom_'.$productCode;
        $html = '<span id="part_uom_col_'.$productCode.'">+</td>'
            . '</tr>'
            . '<td colspan="' . $colspan . '" id="'.$id.'" style="display: none;">';

        if (Mage::registry('contracts_parts_row')) {
            Mage::unregister('contracts_parts_row');
        }

        Mage::register('contracts_parts_row', $uomArray);

        $block = $this->getLayout()->createBlock('epicor_lists/customer_account_contracts_parts_uom');
        /* @var $block Epicor_Lists_Block_Customer_Account_Contracts_Parts_Uom */

        $html .= $block->toHtml();

        return $html;
    }

}
