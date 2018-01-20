<?php

class Epicor_Comm_Block_Adminhtml_Sales_Returns_View_Tab_Details extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function __construct() {
        parent::_construct();
        $this->_title = 'Details';
        $this->setTemplate('epicor_comm/sales/returns/view/details.phtml');
        Mage::register('return_model', $this->getReturn());
        Mage::register('review_display', true);
    }

    /**
     * 
     * @return Epicor_Comm_Model_Customer_Return
     */
    public function getReturn() {
        return Mage::registry('return');
    }

    public function canShowTab() {
        return true;
    }

    public function getTabLabel() {
        return $this->_title;
    }

    public function getTabTitle() {
        return $this->_title;
    }

    public function isHidden() {
        return false;
    }

    public function getLinesHtml() {
        return Mage::app()->getLayout()->createBlock('epicor_comm/adminhtml_sales_returns_view_tab_details_lines')->toHtml();
    }

    public function getAttachmentsHtml() {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        $return = $this->getReturn();

        $html = '';
        if ($helper->checkConfigFlag('return_attachments', $return->getReturnType(), $return->getStoreId())) {
            $html = Mage::app()->getLayout()->createBlock('epicor_comm/adminhtml_sales_returns_view_tab_details_attachments')->toHtml();
        }

        return $html;
    }

}
