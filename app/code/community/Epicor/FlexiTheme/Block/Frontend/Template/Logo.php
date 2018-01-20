<?php

class Epicor_FlexiTheme_Block_Frontend_Template_Logo extends Mage_Page_Block_Html_Header {
        
    public function _construct()
    {
        if(Mage::getSingleton('core/design_package')->getPackageName() == 'flexitheme'){ 
             $this->setTemplate('page/template/logo.phtml');
        }        
    }
}
