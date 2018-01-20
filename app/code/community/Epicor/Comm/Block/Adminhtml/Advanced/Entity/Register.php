<?php

class Epicor_Comm_Block_Adminhtml_Advanced_Entity_Register
        extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_advanced_entity_register';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Uploaded Data');

        parent::__construct();
        
        $this->removeButton('add');
        
        if($this->getRequest()->getParam('back')) {
            $helper = Mage::helper('epicor_comm');
            /* @var $helper Epicor_Comm_Helper_Data */
            $url = $helper->urlDecode($this->getRequest()->getParam('back'));
            $this->_addButton(
                'back',
                array(
                    'label' => Mage::helper('adminhtml')->__('Back'),
                    'onclick' => 'setLocation(\'' . $url . '\')',
                    'class' => 'back',
                ), 
                -1
            );
        }
    }

}
