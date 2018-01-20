<?php

class Epicor_Search_Block_Adminhtml_Config_Form_Field_Grid extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    protected $_productAttributeRenderer;
    protected $_sortDirectionRenderer;

    protected function _getProductAttributeRenderer()
    {
        if (!$this->_productAttributeRenderer) {
            $this->_productAttributeRenderer = Mage::getSingleton('core/layout')->createBlock(
                    'search/adminhtml_config_form_field_productattributes', '', array('is_render_to_js_template' => false)
            );
            
            $this->_productAttributeRenderer->setInputName('productAttribute')
                    ->setClass('rel-to-selected');
        }
        return $this->_productAttributeRenderer;
    }
    
    protected function _getSortDirectionRenderer()
    {
        if (!$this->_sortDirectionRenderer) {
            $this->_sortDirectionRenderer = Mage::getSingleton('core/layout')->createBlock(
                    'search/adminhtml_config_form_field_sortdirection', '', array('is_render_to_js_template' => false)
            );
            
            $this->_sortDirectionRenderer->setInputName('sortdirection')
                    ->setClass('rel-to-selected');
        }
        return $this->_sortDirectionRenderer;
    }

    public function __construct()
    {

        $this->addColumn('code', array(
            'label' => Mage::helper('search')->__('Product Attribute'),
            'renderer' => $this->_getProductAttributeRenderer(),
            'style' => 'width:75px',
        ));
        $this->addColumn('sort_dir', array(
            'label' => Mage::helper('search')->__('Sort Direction'),
            'renderer' => $this->_getSortDirectionRenderer(),
            'style' => 'width:75px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('search')->__('Add');
        parent::__construct();
    }

}
