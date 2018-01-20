<?php

/**
 * Contract frontend actions
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_ContractController extends Epicor_Customerconnect_Controller_Abstract
{

    /**
     * Contract list page
     *
     * @return void
     */
//    public function indexAction()
//    {
//        $contractHelper = Mage::helper('epicor_lists/frontend_contract');
//        if ($contractHelper->contractsDisabled()) {
//            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl());
//            return;
//        }
//        $this->loadLayout();
//        $this->renderLayout();
//    }

    public function preDispatch()
    {
        parent::preDispatch();
        $contractHelper = Mage::helper('epicor_lists/frontend_contract');
        /* @var $contractHelper Epicor_Lists_Helper_Frontend_Contract */

        if ($contractHelper->listsDisabled()) {
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account'));
        }
    }

    /**
     * Products initial grid tab load
     *
     * @return void
     */
    public function productsAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $this->getLayout()->getBlock('contract_products')
                ->setSelected($this->getRequest()->getPost('products', null));
        $this->renderLayout();
    }

    /**
     * Products ajax reload of grid tab
     *
     * @return void
     */
    public function productsgridAction()
    {
        $this->loadEntity();
        $products = $this->getRequest()->getParam('products');
        $this->loadLayout();
        $this->getLayout()->getBlock('contract_products')->setSelected($products);
        $this->renderLayout();
    }

    /**
     * Addresses initial grid tab load
     *
     * @return void
     */
    public function addressesAction()
    {
        $this->loadEntity();
        $this->loadLayout();
        $this->getLayout()->getBlock('contract_addresses')
                ->setSelected($this->getRequest()->getPost('addresses', null));
        $this->renderLayout();
    }

    /**
     * Addresses ajax reload of grid tab
     *
     * @return void
     */
    public function addressesgridAction()
    {
        $this->loadEntity();
        $addresses = $this->getRequest()->getParam('addresses');
        $this->loadLayout();
        $this->getLayout()->getBlock('contract_addresses')->setSelected($addresses);
        $this->renderLayout();
    }

    /**
     * Loads List
     *
     * @return Epicor_Lists_Model_List
     */
    protected function loadEntity()
    {
        $id = $this->getRequest()->getParam('id', null);
        $list = Mage::getModel('epicor_lists/list')->load($id);
        return $list;
    }
    

    /**
     * Contract Ajax  page for getting Shipping Informations
     *
     * @return void
     */
    public function getContractAddressAction()
    {
        $result = array(
            'type' => 'error',
            'html' => '',
            'error' => ''
        );
        $data = $this->getRequest()->getPost();
        if ($data) {
            $contractId = $data['id'];
            $result = Mage::getBlockSingleton('epicor_lists/customer_account_contract_default')->getCustomerSelectedAddress($contractId);
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(json_encode($result));
        }
    }

    /**
     *  Default Contract Save 
     *
     * @return void
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $customerId = Mage::getSingleton('customer/session')->getId();
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $customer->setEccDefaultContract($data['contract_default']);
            $customer->setEccDefaultContractAddress($data['contract_default_address']);
            $customer->save();
            $session = Mage::getSingleton('core/session');
            $session->addSuccess($this->__('Default Contract Saved Successfully'));
            $this->_redirect('*/*/');
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     *  Filter Contract Save 
     *
     * @return void
     */
    public function filterAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $filter[$key] = implode(',', array_filter($this->getRequest()->getParam($key)));
                }
            }
            $customerId = Mage::getSingleton('customer/session')->getId();
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $customer->setEccContractsFilter($filter['contract_filter']);
            $customer->save();
            $session = Mage::getSingleton('core/session');
            $session->addSuccess($this->__('Filter Contract Saved Successfully'));
            $this->_redirect('*/*/');
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Contract Select Page
     *
     * @return void
     */
    public function selectAction()
    {
        $contractHelper = Mage::helper('epicor_lists/frontend_contract');
        if ($contractHelper->contractsDisabled()) {
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl());
            return;
        }
        $quote = Mage::getSingleton('checkout/cart')->getQuote();
        /* @var $quote Epicor_Comm_Model_Quote */
        Mage::register('ecc_checkout_has_items', $quote->hasItems());
        $this->loadLayout();
        $this->renderLayout();
        $sessionHelper = Mage::helper('epicor_lists/session');
        /* @var $sessionHelper Epicor_Lists_Helper_Session */
        if ($sessionHelper->getValue('ecc_optional_select_contract_show')) {
            $sessionHelper->setValue('ecc_optional_select_contract_show', false);
        }
    }

    /**
     * Select Contract ajax reload of grid tab
     *
     * @return void
     */
    public function selectgridAction()
    {
        $quote = Mage::getSingleton('checkout/cart')->getQuote();
        /* @var $quote Epicor_Comm_Model_Quote */
        Mage::register('ecc_checkout_has_items', $quote->hasItems());
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('epicor_lists/contract_select_grid')->toHtml()
        );
    }

    /**
     * Contract Select Action
     */
    public function selectContractAction()
    {
        $contract = $this->getRequest()->getParam('contract');

        $helper = Mage::helper('epicor_lists/frontend_contract');
        /* @var $helper Epicor_Lists_Helper_Frontend_Contract */

        if ($contract && $helper->isValidContractId($contract)) {
            $helper->selectContract($contract);
        }

        if ($contract == -1) {
            $helper->selectContract(null);
            // clear the contract code from the quote, as no contract is selected
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $quote->setEccContractCode(null);
            $quote->save();
        }

        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {
            $returnUrl = $helper->urlDecode($returnUrl);
            $this->_redirectUrl($returnUrl);
        } else {
            $this->_redirect('/');
        }
    }

    /**
     * Contract Select Page
     *
     * @return void
     */
    public function shiptoAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Select Contract ajax reload of grid tab
     *
     * @return void
     */
    public function shiptogridAction()
    {

        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('epicor_lists/contract_shipto_grid')->toHtml()
        );
    }

    /**
     * Contract Select Action
     */
    public function selectShiptoAction()
    {
        Mage::unregister('ecc_contract_allow_change_shipto');
        Mage::register('ecc_contract_allow_change_shipto', true);
        
        $shipto = $this->getRequest()->getParam('shipto');

        $helper = Mage::helper('epicor_lists/frontend_contract');
        /* @var $helper Epicor_Lists_Helper_Frontend_Contract */

        if ($shipto && $helper->isValidShiptoAddressCode($shipto)) {
            $helper->selectContractShipto($shipto);
        }

        if ($shipto == -1) {
            $helper->selectContractShipto(false);
        }

        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {
            $returnUrl = $helper->urlDecode($returnUrl);
            $this->_redirectUrl($returnUrl);
        } else {
            $this->_redirect('/');
        }
    }

}
