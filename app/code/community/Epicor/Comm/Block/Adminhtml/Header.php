<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Header
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Adminhtml_Header extends Mage_Adminhtml_Block_Widget_Form {
    private $header='';
    
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('epicor_comm/header.phtml');
    }
    
    public function setHeaderText($header)
    {
        $this->header=$header;
    }
    
    public function getHeaderText()
    {
        return $this->header;
    }
}


