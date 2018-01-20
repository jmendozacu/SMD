<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Grid
 *
 * @author David.Wylie
 */
class Epicor_B2b_Block_Adminhtml_Customer_Erpaccount_Grid extends Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Grid {

    
     protected function _prepareColumns() {

        parent::_prepareColumns();

        $this->addColumnAfter('pre_reg_password', array(
            'header' => Mage::helper('sales')->__('Pre reg Password'),
            'index' => 'pre_reg_password',
            'width' => '200px',
            'filter'    => false,
            'sortable'  => false,
        ),'onstop');

        return $this;
    }
}