<?php

/*
 *   Grid Container for epicor_comm/erp_mapping_attributes   
 */

class Epicor_Comm_Block_Adminhtml_Mapping_Erpattributes extends Mage_Adminhtml_Block_Widget_Grid_Container {
    /*
     * Construct for epicor_comm/erp_mapping_attributes
     */

    public function __construct() {
        $this->_controller = 'adminhtml_mapping_erpattributes';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('ERP Attribute Mapping');
        $this->_addButtonLabel = Mage::helper('epicor_comm')->__('Add Attribute Mapping');
        parent::__construct();
        $this->_addButton('addbycsv', array(
            'label' => Mage::helper('epicor_comm')->__('Add Attribute Mapping By CSV'),
            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/addbycsv') . '\')',
            'class' => 'add',
        ));
    }

}
