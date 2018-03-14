<?php

class Epicor_Comm_Block_Adminhtml_Config_Form_Field_EwaQuoteDisplayOptions extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

    public function __construct() {

        $this->addColumn('ewaquotesortorder', array(
            'label' => Mage::helper('epicor_comm')->__('EWA Configurator Quote Sort Order'),
            'style' => 'width:150px',
            'renderer' => $this->_getEwaConfiguratorRenderer(),
        ));
        
        $this->_addAfter = false;
        $this->_delete = false;
        parent::__construct();
    }
    protected function _getEwaConfiguratorRenderer() {
            $ewaConfiguratorDisplay = Mage::getSingleton('core/layout')->createBlock(
                'epicor_comm/adminhtml_form_field_ewaQuoteDisplayOptions', '',
                array('is_render_to_js_template' => false)
            );
            $ewaConfiguratorDisplay->setInputName('ewaquotesortorder')
            				  ->setClass('rel-to-selected');
        return $ewaConfiguratorDisplay;
    }    
}