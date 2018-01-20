<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Type
 *
 * @author Paul.Ketelle
 */
class Epicor_Comm_Block_Adminhtml_Sales_Order_View_Addproducts_Renderer_Customprice extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_rowPrice;
    public function render(Varien_Object $row)
    {            
        $html  = $this->getRowPrice($row, true);
        $html .= '<br /><input id="customprice_check_'.$row->getId().'" title="Custom Price" type="checkbox" /><label for="customprice_check_'.$row->getId().'">Custom Price</label>';
        $html .= '<br /><input style="width:60px;display:none;text-align:right;" type="text" class="custom_price" value="'.$this->getRowPrice($row).'" id="customprice_'.$row->getId().'" name="custom_price" value="'.$this->getRowPrice($row).'" />';
        $html .= '<input type="hidden" value="'.$this->getRowPrice($row).'" class="orig_price" id="origprice_'.$row->getId().'" value="'.$this->getRowPrice($row).'" />';
        return $html;
    }

    protected function getRowPrice(Varien_Object $row, $show_currency = false) {
        
        
        $data = floatval($row->getPrice()) * $this->getColumn()->getRate();
        $this->_rowPrice = sprintf("%f", $data);
        
        if($show_currency) {
            $currency_code = $this->getColumn()->getCurrencyCode();
            return Mage::app()->getLocale()->currency($currency_code)->toCurrency($this->_rowPrice);
        }
        else
            return Zend_Locale_Format::toNumber($this->_rowPrice, array('precision'=>2));
    }
}


