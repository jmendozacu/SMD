<?php
/*
 * Setup CSVupload form container for table epicor_comm/erp_mapping_attributes
 */
class Epicor_Comm_Block_Adminhtml_Mapping_Erpattributes_Csvupload extends Mage_Adminhtml_Block_Widget_Form_Container

{

    /*
     * Set up save button and grid for csvupload form 
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_mapping_erpattributes';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Attributes CSV Upload');
        $this->_mode = 'csvupload';

        parent::__construct();

        //$this->_removeButton('back');
        $this->_updateButton('save', 'label', Mage::helper('epicor_comm')->__('Upload'));
    }

}
