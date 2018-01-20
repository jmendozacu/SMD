<?php

/**
 * Order link display
 *
 * @author Gareth.James
 */
class Epicor_Customerconnect_Block_List_Renderer_Linkorder extends Epicor_Common_Block_Renderer_Encodedlinkabstract
{

    protected $_path = 'customerconnect/orders/details';
    protected $_pathTypes = array(
        'O' => array(
            'url' => 'customerconnect/orders/details',
            'module' => 'Epicor_Customerconnect',
            'controller' => 'Orders',
            'action' => 'details',
            'message'=> 'customerconnect/message_request_cuod'
        ),
        'R' => array(
            'url' => 'customerconnect/returns/details',
            'module' => 'Epicor_Customerconnect',
            'controller' => 'Returns',
            'action' => 'details',
            'message'=> 'epicor_comm/message_request_crrd'
        ),
    );
    protected $_key = 'order';
    protected $_addBackUrl = true;
    protected $_permissions = array(
        'module' => 'Epicor_Customerconnect',
        'controller' => 'Orders',
        'action' => 'details',
        'block' => '',
        'action_type' => 'Access',
    );

    public function render(Varien_Object $row)
    {
        $type = 'O';
        
        $orderNumber = $row->getData($this->getColumn()->getIndex());
        if ($orderNumber instanceof Varien_Object) {
            if ($orderNumber->hasData('_attributes')) {
                $attributes = $orderNumber->getData('_attributes');
                if (array_key_exists($attributes->getType(), $this->_pathTypes)) {
                    $type = $attributes->getType();
                }
            }
        }
        
        if (array_key_exists('message', $this->_pathTypes[$type])) {
            $message = Mage::getModel($this->_pathTypes[$type]['message']);
            /* @var $message Epicor_Comm_Model_Message_Request */
            $this->_showLink = $message->isActive();
        }
        
        $this->_path = $this->_pathTypes[$type]['url'];
        $this->_permissions['module'] = $this->_pathTypes[$type]['module'];
        $this->_permissions['controller'] = $this->_pathTypes[$type]['controller'];
        $this->_permissions['action'] = $this->_pathTypes[$type]['action'];
        
        return parent::render($row);
    }

}
