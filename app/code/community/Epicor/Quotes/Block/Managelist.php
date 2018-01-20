<?php


class Epicor_Quotes_Block_Managelist extends Mage_Core_Block_Template
{
    private $_quotes;

    public function getCustomerQuotes()
    {
        if (!$this->_quotes) {
            $this->_quotes = $this->helper('quotes')->getCustomerQuotes(); 
        }
        return $this->_quotes;
    }
    
    public function getViewUrl($quote_id) {
        return $this->getUrl('epicor_quotes/manage/view', array('id' => $quote_id));
    }
    
    public function getRejectUrl($quote_id) {
        return $this->getUrl('epicor_quotes/manage/reject', array('id' => $quote_id));
    }
    public function getDuplicateUrl($quote_id) {
        return $this->getUrl('epicor_quotes/manage/saveDuplicate', array('id' => $quote_id, 'req'=>'dup'));
    }
    
    public function getExpires($quote)
    {
        return $this->helper('quotes')->getHumanExpires($this->getQuote());
    }
}
