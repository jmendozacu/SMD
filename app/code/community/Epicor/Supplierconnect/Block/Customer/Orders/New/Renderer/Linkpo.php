<?php

/**
 * Purchase Order link display
 *
 * @author Gareth.James
 */
class Epicor_Supplierconnect_Block_Customer_Orders_New_Renderer_Linkpo extends Epicor_Common_Block_Renderer_Encodedlinkabstract {
    protected $_path = 'supplierconnect/orders/details';
    protected $_key = 'order';
    protected $_accountType = 'supplier';
    protected $_addBackUrl = true;
    protected $_customParams = array(
        'list_url' => 'supplierconnect/orders/new',
        'list_type' => 'New PO'
    );
    
    protected $_permissions = array(
        'module' => 'Epicor_Supplierconnect',
        'controller' => 'Orders',
        'action' => 'details',
        'block' => '',
        'action_type' => 'Access',
    );
}
