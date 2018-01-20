<?php

/**
 * List ERP Accounts Form
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 * Builds ERP Accounts Contracts Form
 *
 * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Contracts_Form
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Contracts_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public $_form;
    public $_formData;
    public $_account;
    public $_type;
    public $_default = array();
    public $_prefix = array();

    public function __construct()
    {
        parent::__construct();
        $this->_title = 'Contracts';
        $this->_default = array('customer' => 'ERP Account Default', 'erpaccount' => 'Global Default');
        $this->_prefix = array('customer' => 'ecc_', 'erpaccount' => '');
    }

    protected function _prepareForm()
    {

        $this->_form = new Varien_Data_Form();
        $this->_formData = Mage::getSingleton('adminhtml/session')->getFormData(true);

        if (empty($this->_formData)) {
            $this->_formData = $this->_account->getData();
        }
        if (Mage::registry('current_customer')) {
            $fieldset = $this->_form->addFieldset('contracts_default', array('legend' => Mage::helper('epicor_lists')->__('Default Contract Settings')));
            $eccContractsFilter = $fieldset->addField('ecc_contracts_filter', 'multiselect', array(
                'label' => Mage::helper('epicor_lists')->__('Contract Filter'),
                'required' => false,
                'name' => 'ecc_contracts_filter',
                'values' => $this->getContractHtml(),
            ));
            $eccContractsFilter->setAfterElementHtml("
                    <script> 
                     //<![CDATA[
                         var selectedOption = $('ecc_contracts_filter');
                         Event.observe('ecc_contracts_filter', 'change', function(event) {
                         for (i = 0; i < selectedOption.options.length; i++) {
                         var currentOption = selectedOption.options[i];
                         if (currentOption.selected && currentOption.value =='') {
                            for (var i=1; i<selectedOption.options.length; i++) {
                                selectedOption.options[i].selected = false;
                            }                         
                         }
                         }
                         })
                    //]]>
                    </script>
                    ");

            $ecc_default_contract = $fieldset->addField('ecc_default_contract', 'select', array(
                'label' => Mage::helper('epicor_lists')->__('Default Contract'),
                'required' => false,
                'name' => 'ecc_default_contract',
                'values' => $this->getContractHtml(),
            ));
            $ecc_default_contract->setAfterElementHtml("
                    <script> 
                     //<![CDATA[
                     var reloadurl = '" . $this->getUrl('adminhtml/epicorlists_list/fetchaddress/') . "';
                        Event.observe('ecc_default_contract', 'change', function(event) {
                            fetchAddressInList(reloadurl);
                        });
                       fetchAddressInList(reloadurl);
                    //]]>
                    </script>
                    ");


            $fieldset->addField('ecc_default_contract_address', 'select', array(
                'label' => Mage::helper('epicor_lists')->__('Default Contract Address'),
                'required' => false,
                'id' => 'ecc_default_contract_address',
                'name' => 'ecc_default_contract_address',
                'values' => Mage::helper('epicor_lists')->customerSelectedAddressById(),
            ));
        }
        if ($this->_type == 'erpaccount') {
            $fieldset = $this->_form->addFieldset('contracts_form', array('legend' => Mage::helper('epicor_lists')->__('Contracts')));
            $fieldset->addField('allowed_contract_type', 'select', array(
                'label' => Mage::helper('epicor_lists')->__('Allowed Contract Type'),
                'required' => false,
                'name' => 'allowed_contract_type',
                'values' => array(
                    array(
                        'label' => $this->__($this->_default[$this->_type]),
                        'value' => '',
                    ),
                    array(
                        'label' => $this->__('Header Only'),
                        'value' => 'H',
                    ),
                    array(
                        'label' => $this->__('Both Header and Line'),
                        'value' => 'B',
                    ),
                    array(
                        'label' => $this->__('None'),
                        'value' => 'N',
                    ),
                ),
            ));
            $fieldset->addField('required_contract_type', 'select', array(
                'label' => Mage::helper('epicor_lists')->__('Required Contract Type'),
                'required' => false,
                'name' => 'required_contract_type',
                'values' => array(
                    array(
                        'label' => $this->__($this->_default[$this->_type]),
                        'value' => '',
                    ),
                    array(
                        'label' => $this->__('Header'),
                        'value' => 'H',
                    ),
                    array(
                        'label' => $this->__('Either Header or Line'),
                        'value' => 'E',
                    ),
                    array(
                        'label' => $this->__('Optional'),
                        'value' => 'O',
                    ),
                ),
            ));
            $fieldset->addField('allow_non_contract_items', 'select', array(
                'label' => Mage::helper('epicor_lists')->__('Allow Non Contract Items'),
                'required' => false,
                'name' => 'allow_non_contract_items',
                'values' => array(
                    array(
                        'label' => $this->__($this->_default[$this->_type]),
                        'value' => '',
                    ),
                    array(
                        'label' => $this->__('Yes'),
                        'value' => '1',
                    ),
                    array(
                        'label' => $this->__('No'),
                        'value' => '0',
                    ),
                ),
            ));
        }

        if (Mage::getStoreConfig('epicor_lists/contracts/shipto')) {
            $fieldset = $this->_form->addFieldset($this->_prefix[$this->_type] . 'contracts_shipto_form', array('legend' => Mage::helper('epicor_lists')->__('Ship To Settings')));
            if (Mage::getStoreConfig('epicor_lists/contracts/shiptoselection') == 'all') {
                $fieldset->addField($this->_prefix[$this->_type] . 'contract_shipto_default', 'select', array(
                    'label' => Mage::helper('epicor_lists')->__('Contracts Ship To Default'),
                    'required' => false,
                    'name' => $this->_prefix[$this->_type] . 'contract_shipto_default',
                    'values' => array(
                        array(
                            'label' => $this->__('All'),
                            'value' => 'all',
                        ),
                        array(
                            'label' => $this->__('Shoppers Default Ship To'),
                            'value' => 'default',
                        ),
                        array(
                            'label' => $this->__('Specific Ship To'),
                            'value' => 'specific',
                        ),
                        array(
                            'label' => $this->__($this->_default[$this->_type]),
                            'value' => '',
                        ),
                    ),
                ));
            }
            if (Mage::getStoreConfig('epicor_lists/contracts/shiptodate') == 'all') {
                $fieldset->addField($this->_prefix[$this->_type] . 'contract_shipto_date', 'select', array(
                    'label' => Mage::helper('epicor_lists')->__('Contract Ship To Date'),
                    'required' => false,
                    'name' => $this->_prefix[$this->_type] . 'contract_shipto_date',
                    'values' => array(
                        array(
                            'label' => $this->__('All'),
                            'value' => 'all',
                        ),
                        array(
                            'label' => $this->__('Newest Activation Date'),
                            'value' => 'newest',
                        ),
                        array(
                            'label' => $this->__('Oldest Activation Date'),
                            'value' => 'oldest',
                        ),
                        array(
                            'label' => $this->__($this->_default[$this->_type]),
                            'value' => '',
                        ),
                    ),
                ));
            }

            $fieldset->addField($this->_prefix[$this->_type] . 'contract_shipto_prompt', 'select', array(
                'label' => Mage::helper('epicor_lists')->__('Contract Ship To Prompt'),
                'required' => false,
                'name' => $this->_prefix[$this->_type] . 'contract_shipto_prompt',
                'values' => array(
                    array(
                        'label' => $this->__($this->_default[$this->_type]),
                        'value' => '',
                    ),
                    array(
                        'label' => $this->__('Yes'),
                        'value' => '1',
                    ),
                    array(
                        'label' => $this->__('No'),
                        'value' => '0',
                    ),
                ),
            ));
        }

        if (Mage::getStoreConfig('epicor_lists/contracts/header')) {
            $fieldset = $this->_form->addFieldset('contracts_header', array('legend' => Mage::helper('epicor_lists')->__('Header Contract Settings')));
            if (Mage::getStoreConfig('epicor_lists/contracts/headerselection') == 'all') {
                $fieldset->addField($this->_prefix[$this->_type] . 'contract_header_selection', 'select', array(
                    'label' => Mage::helper('epicor_lists')->__('Contract Header Selection'),
                    'required' => false,
                    'name' => $this->_prefix[$this->_type] . 'contract_header_selection',
                    'values' => array(
                        array(
                            'label' => $this->__('All'),
                            'value' => 'all',
                        ),
                        array(
                            'label' => $this->__('Newest'),
                            'value' => 'newest',
                        ),
                        array(
                            'label' => $this->__('Oldest'),
                            'value' => 'oldest',
                        ),
                        array(
                            'label' => $this->__('Most Recently Used'),
                            'value' => 'recent',
                        ),
                        array(
                            'label' => $this->__($this->_default[$this->_type]),
                            'value' => '',
                        ),
                    ),
                ));
            }


            $fieldset->addField($this->_prefix[$this->_type] . 'contract_header_prompt', 'select', array(
                'label' => Mage::helper('epicor_lists')->__('Contract Header Prompt'),
                'required' => false,
                'name' => $this->_prefix[$this->_type] . 'contract_header_prompt',
                'values' => array(
                    array(
                        'label' => $this->__($this->_default[$this->_type]),
                        'value' => '',
                    ),
                    array(
                        'label' => $this->__('Yes'),
                        'value' => '1',
                    ),
                    array(
                        'label' => $this->__('No'),
                        'value' => '0',
                    ),
                ),
            ));

            $fieldset->addField($this->_prefix[$this->_type] . 'contract_header_always', 'select', array(
                'label' => Mage::helper('epicor_lists')->__('Contract Header Always'),
                'required' => false,
                'name' => $this->_prefix[$this->_type] . 'contract_header_always',
                'values' => array(
                    array(
                        'label' => $this->__($this->_default[$this->_type]),
                        'value' => '',
                    ),
                    array(
                        'label' => $this->__('Yes'),
                        'value' => '1',
                    ),
                    array(
                        'label' => $this->__('No'),
                        'value' => '0',
                    ),
                ),
            ));
        }


        if (Mage::getStoreConfig('epicor_lists/contracts/line')) {
            $fieldset = $this->_form->addFieldset('contracts_line', array('legend' => Mage::helper('epicor_lists')->__('Line Contract Selection Settings')));
            if (Mage::getStoreConfig('epicor_lists/contracts/lineselection') == 'all') {
                $fieldset->addField($this->_prefix[$this->_type] . 'contract_line_selection', 'select', array(
                    'label' => Mage::helper('epicor_lists')->__('Contract Line Selection'),
                    'required' => false,
                    'name' => $this->_prefix[$this->_type] . 'contract_line_selection',
                    'values' => array(
                        array(
                            'label' => $this->__('All'),
                            'value' => 'all',
                        ),
                        array(
                            'label' => $this->__('Lowest'),
                            'value' => 'lowest',
                        ),
                        array(
                            'label' => $this->__('Highest'),
                            'value' => 'highest',
                        ),
                        array(
                            'label' => $this->__($this->_default[$this->_type]),
                            'value' => '',
                        ),
                    ),
                ));
            }


            $fieldset->addField($this->_prefix[$this->_type] . 'contract_line_prompt', 'select', array(
                'label' => Mage::helper('epicor_lists')->__('Contract Line Prompt'),
                'required' => false,
                'name' => $this->_prefix[$this->_type] . 'contract_line_prompt',
                'values' => array(
                    array(
                        'label' => $this->__($this->_default[$this->_type]),
                        'value' => '',
                    ),
                    array(
                        'label' => $this->__('Yes'),
                        'value' => '1',
                    ),
                    array(
                        'label' => $this->__('No'),
                        'value' => '0',
                    ),
                ),
            ));

            $fieldset->addField($this->_prefix[$this->_type] . 'contract_line_always', 'select', array(
                'label' => Mage::helper('epicor_lists')->__('Contract Line Always'),
                'required' => false,
                'name' => $this->_prefix[$this->_type] . 'contract_line_always',
                'values' => array(
                    array(
                        'label' => $this->__($this->_default[$this->_type]),
                        'value' => '',
                    ),
                    array(
                        'label' => $this->__('Yes'),
                        'value' => '1',
                    ),
                    array(
                        'label' => $this->__('No'),
                        'value' => '0',
                    ),
                ),
            ));
        }


        $this->_form->setValues($this->_formData);
        $this->setForm($this->_form);

        return parent::_prepareForm();
    }

    /**
     * Get customer contract 
     * @return array
     */
    public function getContractHtml()
    {
        /* @var $customer Epicor_Comm_Model_Customer */
        $customer = Mage::registry('current_customer');
        $contracts = Mage::helper('epicor_common')->customerListsById($customer->getId(), 'filterContracts');
        $contractFilter = $customer->getEccContractsFilter();
        $messages[] = array('label' => 'No Default Contract', 'value' => '');
        foreach ($contracts['items'] as $info) {
            $code = $info['id'];
            $messages[] = array(
                'label' => $info['title'],
                'value' => $code,
            );
        }
        return $messages;
    }

}
