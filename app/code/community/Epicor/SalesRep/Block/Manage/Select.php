<?php
/**
 * SalesRep page select page grid
 *
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Manage_Select extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'epicor_salesrep';
        $this->_controller = 'manage_select';
        $this->_headerText = Mage::helper('epicor_salesrep')->__('Account Selector');
        parent::__construct();
        $this->_removeButton('add');
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::getStoreConfigFlag('epicor_salesrep/general/masquerade_search_dashboard')) {
            Mage::dispatchEvent('adminhtml_widget_container_html_before', array('block' => $this));
            return parent::_toHtml();
        } else {
            return '';
        }
    } 

}