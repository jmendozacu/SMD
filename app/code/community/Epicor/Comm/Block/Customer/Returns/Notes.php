<?php

/**
 * Returns creation page, Notes block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Notes extends Epicor_Comm_Block_Customer_Returns_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('Notes'));
        $this->setTemplate('epicor_comm/customer/returns/notes.phtml');
    }

    public function getNoteText()
    {
        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */
        $note = ($return) ? $return->getNoteText() : '';
        
        return $this->escapeHtml($note);
    }
    public function noteTabLengthLimit(){
        return Mage::getStoreConfig('epicor_comm_returns/notes/tab_length');
    }
    public function noteTabRequired(){
        return Mage::getStoreConfig('epicor_comm_returns/notes/tab_required');
    }

}
