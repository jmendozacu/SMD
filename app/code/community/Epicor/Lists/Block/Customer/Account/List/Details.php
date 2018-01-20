<?php

/**
 * creating  list detail block for add/edit
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Customer_Account_List_Details extends Mage_Core_Block_Template
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

    /**
     * Returns list
     *
     * @return int
     */
    public function getList()
    {
        if (!$this->list) {
            $this->list = Mage::getModel('epicor_lists/list')->load($this->getListId());
        }

        return $this->list;
    }

    /**
     * Returns url used for products
     *
     * @return string
     */
    public function getListCodeValidateUrl()
    {
        $args = array();
        if ($this->getListId()) {
            $args['list_id'] = $this->getListId();
        }
        return $this->getUrl('epicor_lists/list/validateCode', $args);
    }
    
    /**
     * Returns url used for products
     *
     * @return string
     */
    public function getProductUrl()
    {

        $args = array();
        if ($this->getListId()) {

            $args['list_id'] = $this->getListId();
        }

        return $this->getUrl('epicor_lists/list/products', $args);
    }

    /**
     * Returns url used for customers
     *
     * @return string
     */
    public function getCustomerUrl()
    {

        $args = array();
        if ($this->getListId()) {

            $args['list_id'] = $this->getListId();
        }

        return $this->getUrl('epicor_lists/list/customers', $args);
    }

     /**
     * Returns List start date
     *
     * @return string
     */
    public function getStartDate()
    {
        $list = $this->getList();
        $format = Varien_Date::DATETIME_INTERNAL_FORMAT;
        $start_date='';
        if ($list->getStartDate()) {
            $date = Mage::app()->getLocale()->date($list->getStartDate(), $format)->toString($format);
            $dateSplit = explode(' ', $date);
            $start_date = $dateSplit[0];
           
        }
        return $start_date;
    }
    
     /**
     * Returns List end date
     *
     * @return string
     */
    public function getEndDate()
    {
        $list = $this->getList();
        $format = Varien_Date::DATETIME_INTERNAL_FORMAT;
        $end_date='';
        if ($list->getEndDate()) {
            $date = Mage::app()->getLocale()->date($list->getEndDate(), $format)->toString($format);
            $dateSplit = explode(' ', $date);
            $end_date = $dateSplit[0];
           
        }
        return $end_date;
    }
    
    /**
     * Returns List start time
     *
     * @return array
     */
    public function getStartTime()
    {
        $list = $this->getList();
        $format = Varien_Date::DATETIME_INTERNAL_FORMAT;
        $start_time='';
        if ($list->getStartDate()) {
            $date = Mage::app()->getLocale()->date($list->getStartDate(), $format)->toString($format);
            $dateSplit = explode(' ', $date);
            $start_date = $dateSplit[0];
            $start_time = explode(':', $dateSplit[1]);
        }
        return $start_time;
    }

    /**
     * Returns List End time
     *
     * @return array
     */
    public function getEndTime()
    {
        $list = $this->getList();
        $format = Varien_Date::DATETIME_INTERNAL_FORMAT;
        $end_time='';
        if ($list->getEndDate()) {
            $date = Mage::app()->getLocale()->date($list->getEndDate(), $format)->toString($format);
            $dateSplit = explode(' ', $date);
            $end_date = $dateSplit[0];
            $end_time = explode(':', $dateSplit[1]);
        }
        return $end_time;
    }
    
}
