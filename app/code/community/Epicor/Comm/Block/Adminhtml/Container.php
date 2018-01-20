<?php

class Epicor_Comm_Block_Adminhtml_Container extends Mage_Adminhtml_Block_Widget_Form {

   private $_headerText;
   protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('epicor_comm/container.phtml');
    }
    
    public function getHeaderText()
    {
        return $this->_headerText;
    }
    
    public function setHeaderText($text)
    {
        $this->_headerText=$text;
    }
}

