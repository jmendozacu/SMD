<?php
class Epicor_Flexitheme_Block_Adminhtml_Scope extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_layoutOrSkinName;
    protected $_skinOrLayout = '';
    public function __construct()
    {
        parent::__construct();
        // retrieve layout name
        $this->_skinOrLayout = Mage::registry('action_type');       
        if($this->_skinOrLayout == 'layout'){
            $this->_layoutOrSkinName = Mage::getModel('flexitheme/layout')->load($this->getRequest()->getParam('id'))->getLayoutName();
        }else{
            $this->_layoutOrSkinName = Mage::getModel('flexitheme/theme')->load($this->getRequest()->getParam('id'))->getThemeName();
        }
        Mage::register('layoutOrSkinName',trim($this->_layoutOrSkinName));
        $this->_objectId = 'id';
        $this->_blockGroup = 'flexitheme';
        $this->_controller = 'adminhtml_scope';
        
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->_removeButton('add');
       
        $this->_addButtonLabel = Mage::helper('flexitheme')->__('Add Report');

    }
 
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__("Flexitheme Scope Editor for ").$this->_skinOrLayout." : ".$this->_layoutOrSkinName;
    } 
}
