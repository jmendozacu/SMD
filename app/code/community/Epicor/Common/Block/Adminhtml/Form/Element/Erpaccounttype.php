<?php

class Epicor_Common_Block_Adminhtml_Form_Element_Erpaccounttype extends Varien_Data_Form_Element_Abstract
{

    protected $_types;
    protected $_accountType;
    protected $_restrictToType = '';
    protected $_defaultLabel = 'No Account Selected';
    protected $_masterShopDisableTypes = array("salesrep","guest","supplier");     

    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setType('text');
        $this->setExtType('textfield');
        $this->addClass('erpaccount-type-field');
    }

    private function getLabelValue($accountTypeValue, $accountTypeInfo = null)
    {
        $accountId = null;

        $label = Mage::helper('epicor_common')->__($this->_defaultLabel);
        if (!empty($accountTypeInfo)) {
            $accountId = $accountTypeValue;
        } else {
            $accountTypes = $this->getAccountTypes();
            $accountTypeInfo = isset($accountTypes[$accountTypeValue]) ? $accountTypes[$accountTypeValue] : '';

            if (isset($accountTypeInfo['field'])) {
                /* @var $customer Epicor_Comm_Model_Customer */
                $customer = Mage::registry('current_customer');
                if ($customer) {
                    $accountId = $customer->getData($accountTypeInfo['field']);
                }
            }
        }

        if ($accountId) {
            $accountModel = Mage::getModel($accountTypeInfo['model'])->load($accountId);
            if (!$accountModel->isObjectNew()) {
                $label = $accountModel->getName();
            }
        }

        return $label;
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $this->addClass('input-text');
        /* @var $helper Epicor_Common_Helper_Data */
        $helper = Mage::helper('epicor_common');
        $baseFieldName = $this->getName();
        $types = $this->getAccountTypes();

        if (empty($this->_restrictToType)) {
            
            $accountType = $this->getAccountType();
            
            //Disable Master Shoppers for  $this->_masterShopDisableTypes;
            $disableMaster = in_array($accountType, $this->_masterShopDisableTypes);            
            
            /* @var $customer Epicor_Comm_Model_Customer */
            $customer = Mage::registry('current_customer');

            $selectHtml = '<div id="ecc_account_select_type"><select name="' . $this->getName() . '" id="' . $this->getHtmlId() . '"' . $this->serialize($this->getHtmlAttributes()) . '>';
            $typesHtml = '';
            foreach ($types as $value => $info) {
                $selected = $accountType == $value ? ' selected="selected"' : '';
                $selectHtml .= '<option value="' . $value . '"' . $selected . '>' . $info['label'] . '</option>';
                if (isset($info['field'])) {
                    $typesHtml .= $this->getAccountTypeHtml($baseFieldName, $value, $info, $customer);
                }
            }
            $selectHtml .= '</select></div>';
            $ms_shopper = Mage::getStoreConfig("customerconnect_enabled_messages/CUCO_mapping/master_shopper_default_value");
            
            $selectHtml .= '
            <script type="text/javascript">
            //<![CDATA[
                Event.observe("' . $this->getHtmlId() . '", "change", function(event) {
                    el = Event.element(event);
                    accountSelector.switchType("' . $this->getHtmlId() . '", el.options[el.selectedIndex].value);
                    var accountSelect = el.options[el.selectedIndex].value;
                    var notApplicable = document.getElementById("notapplicable"); 
                    var masterShopper = document.getElementById("_accountecc_master_shopper"); 
                    if(accountSelect !="customer") {
                        notApplicable.show();  
                        masterShopper.selectedIndex = 0;
                        masterShopper.hide();                        
                    } else {
                        notApplicable.hide();  
                        masterShopper.selectedIndex = '.$ms_shopper.';
                        masterShopper.show();                        
                    }
                });
            //]]>
            </script>'
            ;

            //If the Account type is not customer then Load this js to hide the master shopper
            if($disableMaster) {
            $selectHtml .= '
            <script type="text/javascript">
            //<![CDATA[
                document.observe("dom:loaded", function(){
                var masterShopper = document.getElementById("_accountecc_master_shopper");
                masterShopper.insertAdjacentHTML("beforeBegin","<div id=\"notapplicable\">Not Applicable</div>");
                masterShopper.selectedIndex = 0;
                masterShopper.hide();
                });
            //]]>
            </script>'
            ; 
            }  else {
            $selectHtml .= '
            <script type="text/javascript">
            //<![CDATA[
                document.observe("dom:loaded", function(){
                var masterShopper = document.getElementById("_accountecc_master_shopper");
                masterShopper.insertAdjacentHTML("beforeBegin","<div id=\"notapplicable\">Not Applicable</div>");
                var notApplicable = document.getElementById("notapplicable"); 
                notApplicable.hide();  
                });
            //]]>
            </script>'
            ; 
            }   

            $currentAccountInfo = isset($types[$accountType]) ? $types[$accountType] : '';
            $display = (isset($currentAccountInfo['field'])) ? '' : ' style="display:none"';
            $labelValue = $this->getLabelValue($accountType);
        } else {
            $display = '';
            $selectHtml = '<input type="hidden" name="' . $this->getName() . '" id="' . $this->getHtmlId() . '" value="' . $this->_restrictToType . '">';
            $typesHtml = $this->getAccountTypeHtml($baseFieldName, $this->_restrictToType, $types[$this->_restrictToType]);
            $labelValue = $this->getLabelValue($this->getValue(), $types[$this->_restrictToType]);
        }

        $html = $selectHtml . $typesHtml;

        $html .= '<input type="hidden" name="account_type_no_label" id="' . $this->getHtmlId() . '_no_account" value="' . $helper->__('No Account Selected') . '" />';
        $html .= '<div id="ecc_account_selector"' . $display . '><span id="' . $this->getHtmlId() . '_label" class="erpaccount_label">' . $labelValue . '</span>';
        $html .= '<button class="form-button" id="' . $this->getHtmlId() . '_trig" onclick="accountSelector.openpopup(\'' . $this->getHtmlId() . '\'); return false;">' . $helper->__('Select') . '</button>';
        $html .= '<button class="form-button" id="' . $this->getHtmlId() . '_remove" onclick="accountSelector.removeAccount(\'' . $this->getHtmlId() . '\'); return false;">' . $helper->__('Remove') . '</button></div>';

        $html .= $this->getAfterElementHtml();

        return $html;
    }

    protected function getAccountTypeHtml($baseFieldName, $type, $info, $customer = null)
    {
        $accountId = ($customer) ? $customer->getData($info['field']) : $this->getValue();
        $fieldName = str_replace('ecc_erp_account_type', $info['field'], $baseFieldName);

        $typesHtml = '<input type="hidden" name="' . $type . '_label" id="' . $this->getHtmlId() . '_' . $type . '_label" value="" />';
        $typesHtml .= '<input type="hidden" name="' . $type . '_field" id="' . $this->getHtmlId() . '_' . $type . '_field" value="' . $info['field'] . '" />';
        $typesHtml .= '<input type="hidden" name="' . $type . '_url" id="' . $this->getHtmlId() . '_' . $type . '_url" value="' . Mage::getUrl($info['url']) . '" />';
        $typesHtml .= '<input type="hidden" name="' . $fieldName . '" id="' . $this->getHtmlId() . '_account_id_' . $type . '" value="' . $accountId . '" class="type_field"/>';
        return $typesHtml;
    }

    protected function getAccountType()
    {
        if (empty($this->_accountType)) {
            $this->_accountType = $this->getValue();
            /* @var $customer Epicor_Comm_Model_Customer */
            $customer = Mage::registry('current_customer');

            if (empty($this->_accountType) && $customer) {

                $helper = Mage::helper('epicor_common/account_selector');
                /* @var $helper Epicor_Common_Helper_Account_Selector */
                $sortedTypes = $helper->getAccountTypesByPriority();
                
                foreach ($sortedTypes as $type) {
                    if (isset($type['field'])) {
                        $accountId = $customer->getData($type['field']);
                        if (!empty($accountId)) {
                            $this->_accountType = $type['value'];
                            break;
                        }
                    } else if ($type['priority'] == 0) {
                        $this->_accountType = $type['value'];
                    }
                }
            }
        }

        return $this->_accountType;
    }

    public function getAccountTypes()
    {
        if (!$this->_types) {
            $helper = Mage::helper('epicor_common/account_selector');
            /* @var $helper Epicor_Common_Helper_Account_Selector */
            $types = $helper->getAccountTypes();

            if (!empty($this->_restrictToType)) {
                $this->_types = array($this->_restrictToType => $types[$this->_restrictToType]);
            } else {
                $this->_types = $types;
            }
        }

        return $this->_types;
    }

}
