<?php

/**
 * List ERP Accounts Form
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Erpaccounts_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->_title = 'ERP Accounts';
    }

    /**
     * Builds List ERP Accounts Form
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Erpaccounts_Form
     */
    protected function _prepareForm()
    {
        $list = Mage::registry('list');
        /* @var $list Epicor_Lists_Model_List */

        if ($list->getTypeInstance()->isSectionEditable('erpaccounts')) {
            $form = new Varien_Data_Form();
            $formData = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (empty($formData)) {
                $formData = $list->getData();
            }

            $fieldset = $form->addFieldset('erpaccounts_form', array('legend' => Mage::helper('epicor_comm')->__('Erp Accounts')));

            $fieldset->addField('erp_account_link_type', 'select', array(
                'label' => Mage::helper('epicor_comm')->__('Erp Account Link Type'),
                'required' => false,
                'name' => 'erp_account_link_type',
                'values' => array(
                    array(
                        'label' => $this->__('B2C'),
                        'value' => 'C',
                    ),
                    array(
                        'label' => $this->__('B2B'),
                        'value' => 'B',
                    ),
                    array(
                        'label' => $this->__('No specific link'),
                        'value' => 'N',
                    ),
                    array(
                        'label' => $this->__('Chosen ERP'),
                        'value' => 'E',
                    ),
                ),
            ))->setAfterElementHtml('<input type="hidden" value="'.Mage::helper("adminhtml")->getUrl("adminhtml/epicorlists_list/erpaccountsessionset/",array()).'" name="ajax_url" id="ajax_url" />');
           
            $checked = $list->getErpAccountsExclusion() == 'Y' ? true : false; 
            $fieldset->addField('erp_accounts_exclusion', 'checkbox', array(
                'label'     => Mage::helper('epicor_lists')->__('Exclude selected ERP Accounts?'),
                'onclick'   => 'this.value = this.checked ? 1 : 0;',
                'name'      => 'erp_accounts_exclusion',
                'checked'    => $checked 
            ));
            
            $selectedErpAccount = $formData['erp_account_link_type'];
            if ($selectedErpAccount) {
                Mage::getSingleton('admin/session')->setLinkTypeValue($selectedErpAccount);
            } else {
                Mage::getSingleton('admin/session')->setLinkTypeValue('');
            }
            $form->setValues($formData);
            $this->setForm($form);
        }

        return parent::_prepareForm();
    }

}