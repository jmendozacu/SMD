<?php

/**
 * Customer Orders list
 */
class Epicor_Lists_Block_Quickorderpad_List_Selector_List extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'quickorderpad_list_selector_list';
        $this->_blockGroup = 'epicor_lists';
        $this->_headerText = Mage::helper('epicor_comm')->__('Lists Search');
            $this->_addButton('close', array(
                'id' => 'close-button',
                'label' => Mage::helper('epicor_comm')->__('Close'),
                       'onclick'   =>  "listsSearchClosePopup()",
                'class' => 'close',
                    ), -100);
    }

    protected function _postSetup() {
        $this->setBoxed(true);
        parent::_postSetup();
    }

}