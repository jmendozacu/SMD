<?php

class Epicor_Common_Block_Adminhtml_Config_Form_Field_Grid extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

    protected $_messageBase;
    protected $_messageType;
    protected $_allowOptions = false;

     
    protected function _getMappingRenderer() {
        $renderer = Mage::getSingleton('core/layout')->createBlock(
                $this->_messageBase . '/adminhtml_config_form_field_' . $this->_messageType . '_mapping', '', array('is_render_to_js_template' => false)
        );
        $renderer->setInputName('index')->setClass('rel-to-selected');
        return $renderer;
    }

    protected function _getTypeRenderer() {
        $renderer = Mage::getSingleton('core/layout')->createBlock(
                'epicor_common/adminhtml_config_form_field_grid_type', '', array('is_render_to_js_template' => false)
        );
        $renderer->setInputName('type')->setClass('rel-to-selected');
        return $renderer;
    }

    protected function _getFilterByRenderer() {
        $renderer = Mage::getSingleton('core/layout')->createBlock(
                'epicor_common/adminhtml_config_form_field_grid_filterby', '', array('is_render_to_js_template' => false)
        );
        $renderer->setInputName('filter_by')->setClass('rel-to-selected');
        return $renderer;
    }

    protected function _getConditionRenderer() {
        $renderer = Mage::getSingleton('core/layout')->createBlock(
                'epicor_common/adminhtml_config_form_field_grid_condition', '', array('is_render_to_js_template' => false)
        );
        $renderer->setInputName('condition')->setClass('rel-to-selected');
        return $renderer;
    }

    protected function _getSortTypeRenderer() {
        $renderer = Mage::getSingleton('core/layout')->createBlock(
                'epicor_common/adminhtml_config_form_field_grid_sorttype', '', array('is_render_to_js_template' => false)
        );
        $renderer->setInputName('sort_type')->setClass('rel-to-selected');
        return $renderer;
    }

    protected function _getOptionsRenderer() {
        $renderer = Mage::getSingleton('core/layout')->createBlock(
                $this->_messageBase . '/adminhtml_config_form_field_' . $this->_messageType . '_options', '', array('is_render_to_js_template' => false)
        );
        $renderer->setInputName('renderer')->setClass('rel-to-selected');
        return $renderer;
    }

    protected function _getRendererRenderer() {
        $renderer = Mage::getSingleton('core/layout')->createBlock(
                $this->_messageBase . '/adminhtml_config_form_field_' . $this->_messageType . '_renderer', '', array('is_render_to_js_template' => false)
        );
        $renderer->setInputName('renderer')->setClass('rel-to-selected');
        return $renderer;
    }
    
    protected function _getContractCodeRenderer() {
        $renderer = Mage::getSingleton('core/layout')->createBlock(
                $this->_messageBase . '/adminhtml_config_form_field_' . $this->_messageType . '_ContractCode', '', array('is_render_to_js_template' => false)
        );
        $renderer->setInputName('renderer')->setClass('rel-to-selected');
        return $renderer;
    }

    public function __construct() {

        $this->addColumn('header', array(
            'label' => Mage::helper('customerconnect')->__('Header'),
            'style' => 'width:100px'
        ));

        $this->addColumn('type', array(
            'label' => Mage::helper('customerconnect')->__('Type'),
            'style' => 'width:50px',
            'renderer' => $this->_getTypeRenderer()
        ));

        if ($this->_allowOptions) {
            $this->addColumn('options', array(
                'label' => Mage::helper('customerconnect')->__('Options'),
                'style' => 'width:50px',
                'renderer' => $this->_getOptionsRenderer()
            ));
        }

        $this->addColumn('index', array(
            'label' => Mage::helper('customerconnect')->__('Mapping'),
            'style' => 'width:75px',
            'renderer' => $this->_getMappingRenderer(),
        ));

        $this->addColumn('filter_by', array(
            'label' => Mage::helper('customerconnect')->__('Filter By'),
            'style' => 'width:75px',
            'renderer' => $this->_getFilterByRenderer()
        ));

        $this->addColumn('condition', array(
            'label' => Mage::helper('customerconnect')->__('Condition'), 
            'style' => 'width:80px',
            'renderer' => $this->_getConditionRenderer()
        ));

        $this->addColumn('sort_type', array(
            'label' => Mage::helper('customerconnect')->__('Sort Type'),
            'style' => 'width:75px',
            'renderer' => $this->_getSortTypeRenderer()
        ));

        $this->addColumn('renderer', array(
            'label' => Mage::helper('customerconnect')->__('Renderer'),
            'style' => 'width:75px',
            'renderer' => $this->_getRendererRenderer()
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('customerconnect')->__('Add');
        parent::__construct();
    } 

}