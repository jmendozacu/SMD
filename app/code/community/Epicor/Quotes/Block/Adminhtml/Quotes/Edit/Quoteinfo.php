<?php

class Epicor_Quotes_Block_Adminhtml_Quotes_Edit_Quoteinfo extends Epicor_Quotes_Block_Adminhtml_Quotes_Edit_Abstract
{

    public function getExpires()
    {
        return $this->helper('quotes')->getHumanExpires($this->getQuote());
    }

    public function getUpdatedAt() {
        return $this->helper('quotes')->getLocalDate($this->getQuote()->getUpdatedAt());
    }
    
}
