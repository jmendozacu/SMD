<?php

class Epicor_Comm_Block_Adminhtml_Locations_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_locations';
        $this->_blockGroup = 'epicor_comm';
        $this->_mode = 'edit';

        $this->_addButton('save_and_continue', array(
            'label' => Mage::helper('epicor_comm')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_updateButton('save', 'label', Mage::helper('epicor_comm')->__('Save'));

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'edit_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
                }
            }
 
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * 
     * @return Epicor_Comm_Model_Location
     */
    public function getLocation()
    {
        if (!$this->_location) {
            $this->_location = Mage::registry('location');
        }
        return $this->_location;
    }

    public function getHeaderText()
    {
        $location = $this->getLocation();
        $code = $location->getCode();
        return Mage::helper('epicor_comm')->__('Location: %s', $code);
    }

}
