<?php

/**
 * Description of Encodedlinkabstract
 *
 * @author Paul.Ketelle
 */
class Epicor_Common_Block_Renderer_Encodedlinkabstract extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    protected $_path = '*/*/*';
    protected $_key = 'key';
    protected $_accountType = 'customer';
    protected $_addBackUrl = false;
    protected $_customParams = array();
    protected $_permissions = array();
    protected $_showLink = true;

    public function render(Varien_Object $row) {

        $link = '';
        
        $id = $row->getData($this->getColumn()->getIndex());
        
        $accessHelper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        if($this->_showLink && (empty($this->_permissions) || $accessHelper->customerHasAccess($this->_permissions['module'], $this->_permissions['controller'], $this->_permissions['action'], $this->_permissions['block'], $this->_permissions['action_type']))) {
            
            $helper = Mage::helper('epicor_comm');
            /* @var $helper Epicor_Comm_Helper_Data */

            if($this->_accountType == 'customer') {
                $erp_account_number = $helper->getErpAccountNumber();
            } else if($this->_accountType == 'supplier') {
                $erp_account_number = $helper->getSupplierAccountNumber();
            }

            
            $item_requested = $helper->urlEncode($helper->encrypt($erp_account_number . ']:[' . $id));
            $params = array($this->_key => $item_requested);

            if($this->_addBackUrl) {
                $params['back'] = $helper->urlEncode(Mage::getUrl('*/*/*',$this->getRequest()->getParams()));
            }

            if(!empty($this->_customParams)) {
                foreach($this->_customParams as $key => $val) {
                    if(strpos($key,'_url') !== false) {
                        $val = $helper->urlEncode(Mage::getUrl($val));
                    } 

                    $params[$key] = $val;
                }
            }

            $url = Mage::getUrl($this->_path,$params);

            if (!empty($id)) {
                $link = '<a href="' . $url . '" >' . $id . '</a>';
            }
            
        } else {
            if (!empty($id)) {
                $link = $id;
            }
        }
        
        return $link;
    }

}

