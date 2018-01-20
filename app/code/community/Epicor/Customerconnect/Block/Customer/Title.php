<?php

/**
 * Customer connect details page title class
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 * 
 */
class Epicor_Customerconnect_Block_Customer_Title extends Mage_Core_Block_Template
{

    protected $_title;
    protected $_reorderUrl;
    protected $_reorderType;
    protected $_returnType;
    protected $_returnUrl;

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('customerconnect/title.phtml');
    }

    /**
     * Returns whether an entity can be reordered or not
     * 
     * @return boolean
     */
    public function canReorder()
    {
        if (Mage::getStoreConfig('sales/reorder/allow')) {
            $helper = Mage::helper('epicor_common/access');
            /* @var $helper Epicor_Common_Helper_Access */

            return $helper->customerHasAccess('Epicor_Customerconnect', $this->_reorderType, 'reorder', '', 'Access');
        }
    }

    /**
     * Returns whether an entity can be returned or not
     * 
     * @return boolean
     */
    public function canReturn()
    {
        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        $returnsHelper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        $canReturn = false;

        if ($returnsHelper->isReturnsEnabled() && $returnsHelper->checkConfigFlag('allow_create')) {
            $canReturn = $helper->customerHasAccess(
                    'Epicor_Comm', 'Returns', 'createReturnFromDocument', '', 'Access'
            );
        }

        return $canReturn;
    }

    /**
     * Gets the Detail page title
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Gets the Reorder Url for this entity
     * 
     * @return string
     */
    public function getReorderUrl()
    {
        return $this->_reorderUrl;
    }

    /**
     * Gets the Reorder Url for this entity
     * 
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->_returnUrl;
    }

    /**
     * Returns whether an entity can be edited or not
     * 
     * @return boolean
     */
    public function canEdit()
    {
        return false;
    }

    /**
     * Returns whether an entity can be deleted or not
     * 
     * @return boolean
     */
    public function canDelete()
    {
        return false;
    }

}
