<?php

/**
 * Line comment display
 *
 * @author Gareth.James
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_List_Renderer_Confirm extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('customerconnect/messaging');
        /* @var $helper Epicor_Customerconnect_Helper_Messaging */

        $status = $helper->getErpquoteStatusDescription($row->getQuoteStatus(), '', 'state');

        $disabled = '';
        
        $erpAccount = $helper->getErpAccountInfo();
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
        $currencyCode = $erpAccount->getCurrencyCode(Mage::app()->getStore()->getBaseCurrencyCode());

        if (!Mage::registry('rfqs_editable')
            || $status != Epicor_Customerconnect_Model_Config_Source_Quotestatus::QUOTE_STATUS_AWAITING
            || $row->getQuoteEntered() != 'Y'
            || !$currencyCode
        ) {
            $disabled = 'disabled="disabled"';
        }

        $html = '<input type="checkbox" name="confirmed[]" value="' . $row->getId() . '" id="rfq_confirm_' . $row->getId() . '" class="rfq_confirm" ' . $disabled . '/>'
            . '<input type="hidden" name="rfq[' . $row->getId() . '][quote_number]" value="' . $row->getQuoteNumber() . '"/>'
            . '<input type="hidden" name="rfq[' . $row->getId() . '][quote_sequence]" value="' . $row->getQuoteSequence() . '"/>'
            . '<input type="hidden" name="rfq[' . $row->getId() . '][recurring_quote]" value="' . $row->getRecurringQuote() . '"/>'
            . '<input type="hidden" name="rfq[' . $row->getId() . '][amount]" id="rfq_' . $row->getId() . '_total" value="' . $row->getOriginalValue() . '"/>'
        ;
        
        $html .= '<p id="rfq_' . $row->getId() . '_customer_reference_box" style="display:none">';
        $html .= '<label for="rfq_' . $row->getId() . '_customer_reference">Reference:</label>';
        $html .= '<input type="text" name="rfq[' . $row->getId() . '][customer_reference]" id="rfq_' . $row->getId() . '_customer_reference" value=""/>';
        $html .= '</p>';

        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        if ($disabled == '' && $helper->customerHasAccess('Epicor_Quotes', 'Manage', 'accept', '', 'Access')) {
            $quoteNumber = $row->getQuoteNumber();

            $quote = Mage::getModel('quotes/quote');
            /* @var $quote Epicor_Quotes_Model_Quote */
            $quote->load($quoteNumber, 'quote_number');

            if ($quote->getId() && $quote->isAcceptable()) {
                $helper = Mage::helper('customerconnect');
                /* @var $helper Epicor_Customerconnect_Helper_Data */

                $return = Mage::getUrl('quotes/manage/accept/', array('id' => $quote->getId()));
                $img = $this->getSkinUrl('epicor/customerconnect/images/checkout_icon.gif');
                $html .= '<a href="' . $return . '" class="rfq_checkout_link">'
                    . ' <img src="' . $img . '" alt="' . $this->__('Checkout') . '" /> '
                    . '</a>';
            }
        }

        return $html;
    }

}
