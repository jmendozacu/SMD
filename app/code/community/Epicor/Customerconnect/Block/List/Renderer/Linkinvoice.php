<?php

/**
 * Invoice link display
 *
 * @author Gareth.James
 */
class Epicor_Customerconnect_Block_List_Renderer_Linkinvoice extends Epicor_Common_Block_Renderer_Encodedlinkabstract {
    protected $_path = 'customerconnect/invoices/details';
    protected $_key = 'invoice';
    protected $_addBackUrl = true;
    
    protected $_permissions = array(
        'module' => 'Epicor_Customerconnect',
        'controller' => 'Invoices',
        'action' => 'details',
        'block' => '',
        'action_type' => 'Access',
    );
}
