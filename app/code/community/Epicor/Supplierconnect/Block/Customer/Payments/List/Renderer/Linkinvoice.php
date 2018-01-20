<?php

/**
 * Purchase Invoice link display
 *
 * @author Gareth.James
 */
class Epicor_Supplierconnect_Block_Customer_Payments_List_Renderer_Linkinvoice extends Epicor_Common_Block_Renderer_Encodedlinkabstract {
    protected $_path = 'supplierconnect/invoices/details';
    protected $_key = 'invoice';
    protected $_accountType = 'supplier';
    protected $_addBackUrl = true;
    
    protected $_permissions = array(
        'module' => 'Epicor_Supplierconnect',
        'controller' => 'Invoices',
        'action' => 'details',
        'block' => '',
        'action_type' => 'Access',
    );
}
