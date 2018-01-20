<?php

/**
 * Related documents attribute renderer - renders serialized data
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Form_Element_Relateddocuments extends Epicor_Common_Lib_Varien_Data_Form_Element_Serialized {

    protected $_columns = array(
        'filename' => array(
            'type' => 'text',
            'label' => 'Filename'
        ),
        'description' => array(
            'type' => 'text',
            'label' => 'Description'
        ),
        'is_erp_document' => array(
            'type' => 'checkbox',
            'label' => 'From ERP?',
            'disabled' => true,
            'default' => 0
        )
    );

}