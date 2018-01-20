<?php

class Epicor_Comm_Controller_Adminhtml_Abstract extends Mage_Adminhtml_Controller_Action {

    protected $_aclId = null;
    protected $_translatePath;
    
    protected function _isAllowed() {
        
        if($this->_aclId){
            return Mage::getSingleton('admin/session')->isAllowed($this->_aclId);
        }else{
            return true;
        }
    }
    
    protected function setAlcId($aclId){
        $this->_aclId = $aclId;
    }

}
