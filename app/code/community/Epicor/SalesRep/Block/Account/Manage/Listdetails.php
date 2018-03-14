<?php

/**
 * creating  list detail block for add/edit
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Salesrep_Block_Account_Manage_Listdetails extends Epicor_Lists_Block_Customer_Account_List_Details
{

    protected $listId = null;
    protected $list = false;

    /**
     * Returns list
     *
     * @return Epicor_Lists_Model_List
     */
    public function getListId()
    {
        if ($this->getRequest()->getParam('id')) {
            $listId = $this->getRequest()->getParam('id');
            return $listId;
        }
    }
 
     /**
     * Returns url used for salesrep accounts
     *
     * @return string
     */
    public function getErpAccountsUrl()
    {

        $args = array();
        if ($this->getListId()) {

            $args['list_id'] = $this->getListId();
        }
        return $this->getUrl('epicor_salesrep/account_manage/listerpaccounts', $args);
    }
     /**
     * Returns url used for salesrep pricing rules
     *
     * @return string
     */
    public function getSalesrepPricingRuleUrl()
    {

        $args = array();
        if ($this->getListId()) {

            $args['list_id'] = $this->getListId();
        }
        return $this->getUrl('epicor_salesrep/account_manage/testPricingRules', $args);
    }
     /**
     * Returns url used for salesrep id
     *
     * @return string
     */
    public function getSalesrepId()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();        
        return $customer->getSalesRepAccountId();                
    }
     /**
     * Returns owner id used to create list
     *
     * @return string
     */
    public function getOwnerId()
    {
        return $this->list->getOwnerId();     
    }

}   