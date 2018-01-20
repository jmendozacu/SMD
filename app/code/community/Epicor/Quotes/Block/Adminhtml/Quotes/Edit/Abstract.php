<?php

class Epicor_Quotes_Block_Adminhtml_Quotes_Edit_Abstract extends Mage_Adminhtml_Block_Widget
{

    private $_quote;
    private $_quoteNoteType;
    private $_singleNoteType;
    private $_lineNoteType;

    /**
     * 
     * @return Epicor_Quotes_Model_Quote
     */
    public function getQuote()
    {
        if (!$this->_quote) $this->_quote = Mage::registry('quote');
        return $this->_quote;
    }

    /**
     * 
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->getQuote()->getCustomer();
    }

    /**
     * 
     * @return string
     */
    public function getQuoteNoteType()
    {
        if (empty($this->_quoteNoteType)) {
            $this->_quoteNoteType = Mage::getStoreConfig('epicor_quotes/notes/quote_note_type');
        }
        
        return $this->_quoteNoteType;
    }

    /**
     * 
     * @return string
     */
    public function getLineNoteType()
    {
        if (empty($this->_lineNoteType)) {
            $this->_lineNoteType = Mage::getStoreConfig('epicor_quotes/notes/line_note_type');
        }
        
        return $this->_lineNoteType;
    }

    /**
     * 
     * @return string
     */
    public function getSingleNoteType()
    {
        if (empty($this->_singleNoteType)) {
            $this->_singleNoteType = Mage::getStoreConfig('epicor_quotes/notes/single_note_type');
        }
        
        return $this->_singleNoteType;
    }

}
