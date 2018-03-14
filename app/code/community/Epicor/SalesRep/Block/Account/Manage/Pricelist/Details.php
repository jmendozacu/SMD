<?php

/**
 * creating  list detail block for add/edit
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Salesrep_Block_Account_Manage_Pricelist_Details extends Epicor_Lists_Block_Customer_Account_List_Details
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
            $listId = base64_decode($this->getRequest()->getParam('id'));
            return $listId;
        }
    }   
}   