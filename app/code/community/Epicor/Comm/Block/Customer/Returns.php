<?php

class Epicor_Comm_Block_Customer_Returns extends Mage_Core_Block_Template
{

    private $_stepData = array(
        'login' => array(
            'label' => 'Login',
            'block' => 'epicor_comm/customer_returns_login',
            'remove_section' => 'attachments'
        ),
        'return' => array(
            'label' => 'Return',
            'block' => 'epicor_comm/customer_returns_return'
        ),
        'products' => array(
            'label' => 'Products to Return',
            'block' => 'epicor_comm/customer_returns_products'
        ),
        'attachments' => array(
            'label' => 'Additional Attachments',
            'block' => 'epicor_comm/customer_returns_attachments'
        ),
    );
    

    public function _construct()
    {
        if(Mage::getStoreConfig('epicor_comm_returns/notes/tab_required')){
            $this->_stepData['notes'] = array(
                'label' => 'Notes / Comments',
                'block' => 'epicor_comm/customer_returns_notes'             
            );   
        }
        $this->_stepData['review'] = array(
            'label' => 'Review',
            'block' => 'epicor_comm/customer_returns_review' 
        ); 
        
        parent::_construct();
        $this->setTitle($this->__('Returns'));
        $this->setTemplate('epicor_comm/customer/returns/index.phtml');

        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        $type = $helper->getReturnUserType();

        $enabled = true;

        if (empty($type)) {
            $guestEnabled = $helper->checkConfigFlag('return_attachments', 'guests');
            $b2cEnabled = $helper->checkConfigFlag('return_attachments', 'b2c');
            $b2bEnabled = $helper->checkConfigFlag('return_attachments', 'b2b');

            if (!$guestEnabled && !$b2cEnabled && !$b2bEnabled) {
                unset($this->_stepData['attachments']);
            }
        } else {
            $enabled = $helper->checkConfigFlag('return_attachments');
        }

        if (!$enabled) {
            unset($this->_stepData['attachments']);
        }
    }

    /**
     * Get active step
     *
     * @return string
     */
    public function getActiveStep()
    {
        return $this->isCustomerLoggedIn() ? 'return' : 'login';
    }

    public function isCustomerLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /**
     * Get 'one step checkout' step data
     *
     * @return array
     */
    public function getSteps()
    {
        if ($this->isCustomerLoggedIn()) {
            unset($this->_stepData['login']);
        }

        return $this->_stepData;
    }

}
