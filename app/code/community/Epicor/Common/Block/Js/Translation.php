<?php

/**
 * Js Translation block, used for getting the translations of javascript messages
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */

class Epicor_Common_Block_Js_Translation  extends Mage_Core_Block_Template
{

    protected $_translations = array();
    
    protected function _construct()
    {
        parent::_construct();
        
        if ($this->hasData('template')) {
            $this->setTemplate($this->getData('template'));
        } else {
            $this->setTemplate('epicor_comm/js/translation.phtml');
        }
    }
    
    public function getTranslations()
    {
        return $this->_translations;
    }
    
    public function setTranslations($translations){
        $this->_translations = $translations;
        return $this;
        
    }
    
    public function addTranslation($message, $translatedMessage = null){
        if(!$translatedMessage){
            $translatedMessage = $this->__($message);
        }
        $this->_translations[$message] = $translatedMessage;
        return $this;
    }

}
