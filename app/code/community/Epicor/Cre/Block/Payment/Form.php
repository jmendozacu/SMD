<?php

/**
 * 
 * CRE payment method form block
 * 
 * @category    Epicor
 * @package     Epicor_Cre
 * @author      Epicor Web Sales Team
 */
class Epicor_Cre_Block_Payment_Form extends Mage_Payment_Block_Form_Cc
{

    private $_savedcards;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('cre/payment/form.phtml');
    }

    public function hasCustomerSavedCards()
    {
        return $this->getCustomerCards()->getSize() >= 1;
    }

    public function getCustomerCards()
    {
        if (!$this->_savedcards) {
            $this->_savedcards = $this->helper('cre')->getCustomerSavedCards();
        }
        return $this->_savedcards;
    }

}
