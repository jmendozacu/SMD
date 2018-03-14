<?php

/**
 * CRE payment info block, used on order details page
 * 
 * @category    Epicor
 * @package     Epicor_Cre
 * @author      Epicor Web Sales Team
 */
class Epicor_Cre_Block_Payment_Info extends Mage_Payment_Block_Info_Cc
{

    private $_cre;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('cre/payment/info.phtml');
    }

    public function getPaymentTitle()
    {
        return Mage::getStoreConfig('payment/cre/title');
    }

    public function getCardType()
    {
        return '<img src="' . Mage::helper('cre')->getCardTypeImage($this->getInfo()->getCcType()) . '" alt="' . $this->getInfo()->getCcType() . '"/>';
    }

    public function getCardNumber()
    {
        return '**** **** **** ' . $this->getInfo()->getCcLast4();
    }

    public function getCcvToken()
    {
        return $this->getInfo()->getCcvToken();
    }

    public function getCvvToken()
    {
        return $this->getInfo()->getCvvToken();
    }

}
