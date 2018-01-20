<?php

/**
 * Manufacturers attribute display, displays serialized manufacturer data
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Form_Element_Manufacturers extends Epicor_Common_Lib_Varien_Data_Form_Element_Serialized {

    protected $_columns = array(
        'name' => array(
            'type' => 'text',
            'label' => 'Name'
        ),
        'product_code' => array(
            'type' => 'text',
            'label' => 'Product Code'
        )
    );

}