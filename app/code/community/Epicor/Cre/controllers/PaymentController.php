<?php

/**
 * CRE Payment controller 
 * 
 * @category    Epicor
 * @package     Epicor_Cre
 * @author      Epicor Web Sales Team
 */
class Epicor_Cre_PaymentController extends Mage_Core_Controller_Front_Action
{

    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Session Expired');
            exit;
        }
    }

    
    /**
     * Save the Card Informations in the quote once the token was received
     *      
     * 
     *
     * 
     * @return json
     */
    
    public function opsSaveCreQuoteAction()
    {
        
        $parameters  = $this->getRequest()->getPost('parameters');
        $helper      = Mage::helper('epicor_comm/Messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        $cre_payment_method = Mage::getModel('cre/paymentMethod');
        /* @var $cre_payment_method Epicor_Cre_Model_PaymentMethod */        
        $encodeParam = json_decode($parameters, true);
        Mage::log('Recieved :' . "\n" . $parameters, null, 'cre.log');
        $success = false;
        $cart        = Mage::getSingleton('checkout/cart');
        $quote       = $cart->getQuote();         
        if(!empty($encodeParam['success'])) {
            Mage::log('Recieved QuoteId:' . "\n" . $quote->getId(), null, 'cre.log');
            $token       = $encodeParam['token'];
            $transid     = $encodeParam['transid'];
            $lastfour    = $encodeParam['lastfour'];
            $masked      = $encodeParam['masked'];
            $cardtype    = $encodeParam['cardtype'];
            $expdate     = $encodeParam['expdate'];
            $cvv         = $encodeParam['cvv'];
            $pos = 2;
            $Month       = substr($expdate, 0, $pos);
            $Year        = substr($expdate, -2);
            $ccType      = $helper->getCardTypeMapping('cre', $cardtype, $helper::ERP_TO_MAGENTO);
            $tokenCheck       = $cre_payment_method->getToken($encodeParam, $token);
            $quote->getPayment()->setMethod('cre');
            $quote->getPayment()->setCcvToken($token);
            $quote->getPayment()->setCvvToken($cvv);
            $quote->getPayment()->setCcNumberEnc($masked);
            $quote->getPayment()->setCcType($ccType);
            $quote->getPayment()->setCcExpMonth($Month);
            $quote->getPayment()->setCcExpYear($Year);
            $quote->getPayment()->setCcLast4($lastfour);
            $quote->getPayment()->setCreTransactionId($transid);
            $quote->getPayment()->save();
            $success = true;
           $error = "";  
        } else {
          $error = $encodeParam['error'];  
          Mage::log('Recieved QuoteId:' . "\n" . $quote->getId(), null, 'cre.log');
        }
        $return_data = array(
            'success' => $success,
            'msg' => $error
        );
        Mage::App()->getResponse()->setHeader('Content-type', 'application/json');
        Mage::App()->getResponse()->setBody(json_encode($return_data));        
    }    
    

}
