<?php

class Epicor_FlexiTheme_Block_Frontend_Template_Welcome extends Mage_Page_Block_Html_Header {
        
    public function _construct()
    {
        $this->setTemplate('page/template/welcome.phtml');
    }
}
