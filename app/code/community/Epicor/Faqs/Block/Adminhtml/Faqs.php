<?php

/**
 * Faqs List admin grid container
 * @category   Epicor
 * @package    Faq
 * @author     Epicor Websales Team
 *
 */
class Epicor_Faqs_Block_Adminhtml_Faqs extends Mage_Adminhtml_Block_Widget_Grid_Container {

    /**
     * Block constructor
     */
    public function __construct() {
        $this->_blockGroup = 'epicor_faqs';
        $this->_controller = 'adminhtml_faqs';
        $this->_headerText = Mage::helper('epicor_faqs')->__('Manage F.A.Q.');

        parent::__construct();
        //Checking user  permission to save and adding/removing a save button
        if (Mage::helper('epicor_faqs')->isActionAllowed('save')) {
            $this->_updateButton('add', 'label', Mage::helper('epicor_faqs')->__('Add New F.A.Q.'));
        } else {
            $this->_removeButton('add');
        }
    }

}
