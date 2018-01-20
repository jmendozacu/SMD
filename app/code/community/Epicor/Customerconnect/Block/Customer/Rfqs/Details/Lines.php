<?php

/**
 * RFQ lines grid container 
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'customer_rfqs_details_lines';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = ''; //Mage::helper('customerconnect')->__('Lines');

        if (Mage::registry('rfqs_editable')) {
            $this->_addButton(
                'add_line',
                array(
                'id' => 'add_line',
                'label' => Mage::helper('customerconnect')->__('Quick Add'),
                'class' => 'add',
                ), -1
            );

            $this->_addButton(
                'add_search',
                array(
                'id' => 'add_search',
                'label' => Mage::helper('customerconnect')->__('Add by Search'),
                'class' => 'show-hide',
                ), -1
            );
            
            $this->_addButton(
                'newline_button',
                array(
                'id' => 'newline_button',
                'label' => '',
                'class' => '',
                ), 0
            );
            
            
            $this->_addButton(
                'clone_selected',
                array(
                'id' => 'clone_selected',
                'label' => Mage::helper('customerconnect')->__('Clone Selected'),
                'class' => 'go',
                ), 1
            );
            
            $this->_addButton(
                'delete_selected',
                array(
                'id' => 'delete_selected',
                'label' => Mage::helper('customerconnect')->__('Delete Selected'),
                'class' => 'delete',
                ), 1
            );
        }
    }

    protected function _postSetup()
    {
        parent::_postSetup();
    }

}
