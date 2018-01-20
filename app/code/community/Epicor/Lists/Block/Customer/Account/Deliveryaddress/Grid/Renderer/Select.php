<?php

/**
 * Column Renderer for deliveryaddress Select Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Customer_Account_Deliveryaddress_Grid_Renderer_Select extends Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Action
{

    public function render(Varien_Object $row)
    {
        $actions = $this->getColumn()->getActions();
        if (empty($actions) || !is_array($actions)) {
            return '&nbsp;';
        }

        if ($this->getColumn()->getLinks() == true) {

            $helper = Mage::helper('epicor_lists/frontend_restricted');
            /* @var $helper Epicor_Lists_Helper_Frontend_Restricted */

            $html = '';

            if ($this->isSelectedAddress($row)) {
                $html .= $this->__('Currently Selected');
                foreach ($actions as $action) {
                    if (is_array($action)) {
                        if (($html != '') && ($action['caption'] != 'Select')) {
                            $html .= '<span class="action-divider">' . ($this->getColumn()->getDivider() ?: ' | ') . '</span>';
                            $html .= $this->_toLinkHtml($action, $row);
                        }
                    }
                }
            } else {
                foreach ($actions as $action) {
                    if (is_array($action)) {
                        if ($html != '') {
                            $html .= '<span class="action-divider">' . ($this->getColumn()->getDivider() ?: ' | ') . '</span>';
                        }
                        $html .= $this->_toLinkHtml($action, $row);
                    }
                }
            }

            $ajaxDeliveryAddressUrl = Mage::getUrl('epicor_lists/list/changeshippingaddress', $helper->issecure());
            $cartPopupurl = Mage::getUrl('epicor_lists/list/cartpopup', $helper->issecure());
            $selectAddress = Mage::getUrl('epicor_lists/list/selectaddressajax', $helper->issecure());
            $html .= '<input type="hidden" name="ajaxdeliveryaddressurl" id="ajaxdeliveryaddressurl" value="' . $ajaxDeliveryAddressUrl . '">';
            $html .= '<input type="hidden" name="ajaxcode" id="ajaxcode" value="' . $row->getEntityId() . '">';
            $html .= '<input type="hidden" name="cartpopupurl" id="cartpopupurl" value="' . $cartPopupurl . '">';
            $html .= '<input type="hidden" name="selectbranch" id="selectaddress" value="' . $selectAddress . '">';
            return $html;
        } else {
            return parent::render($row);
        }
    }

    /**
     * Determines if this row is the currently selected address
     * 
     * @param Epicor_Comm_Model_Customer_Address $row
     * 
     * @return boolean
     */
    protected function isSelectedAddress($row)
    {
        $helper = Mage::helper('epicor_lists/frontend_restricted');
        /* @var $helper Epicor_Lists_Helper_Frontend_Restricted */
        $matchValue = $this->getRestrictionAddressMatchValue();
        
        if ($helper->isMasquerading()) {
            $addressValue = $row->getErpAddressCode();
        } else {
            $addressValue = $row->getEntityId();
        }

        return $matchValue == $addressValue;
    }

    /**
     * Gets the value to check for the restriction address
     * 
     * @return mixed (string/int)
     */
    protected function getRestrictionAddressMatchValue()
    {
        $matchVal = Mage::registry('ecc_select_restrction_address_val');
        
        if (is_null($matchVal) == false) {
            return $matchVal;
        }
        
        $helper = Mage::helper('epicor_lists/frontend_restricted');
        /* @var $helper Epicor_Lists_Helper_Frontend_Restricted */
        $address = $helper->getRestrictionAddress();
        if ($helper->isMasquerading()) {
            $matchVal =  $address->getErpAddressCode();
        } else {
            if ($address instanceof Mage_Sales_Model_Quote_Address) {
                $matchVal = $address->getCustomerAddressId();
            } else {
                $matchVal = $address->getId();
            }
        }
        
        Mage::unregister('ecc_select_restrction_address_val');
        Mage::register('ecc_select_restrction_address_val', $matchVal);
        return $matchVal;
    }

}
