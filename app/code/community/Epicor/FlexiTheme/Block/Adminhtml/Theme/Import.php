<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Import extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_theme';
        $this->_blockGroup = 'flexitheme';
        $this->_mode = 'import';


        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Import'));
    }

    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Import Theme');
    }

}
