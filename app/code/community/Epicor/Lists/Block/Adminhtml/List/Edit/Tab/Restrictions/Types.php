<?php

/**
 * List Restricted type form
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Restrictions_Types extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->_title = 'Restricted Purchase';
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
        $form = new Varien_Data_Form();
        $formData = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (empty($formData)) {
            $formData = $list->getData();
        }
        if (Mage::getSingleton('admin/session')->getRestrictionTypeValue()) {
            $formData['restriction_type'] = Mage::getSingleton('admin/session')->getRestrictionTypeValue();
        }
        $fieldset = $form->addFieldset('restricted_purchases_type_form', array('legend' => Mage::helper('epicor_comm')->__('Restricted Purchase')));
        $fieldset->addField('restriction_type', 'select', array(
                'label' => Mage::helper('epicor_comm')->__('Restricted Purchase Type'),
                'required' => false,
                'name' => 'restriction_type',
                'onchange' => 'loadRestrictionsGrid(this.value)',
                'values' => array(
                    array(
                        'label' => $this->__('Address'),
                        'value' => Epicor_Lists_Model_List_Address_Restriction::TYPE_ADDRESS,
                    ),
                    array(
                        'label' => $this->__('Country'),
                        'value' => Epicor_Lists_Model_List_Address_Restriction::TYPE_COUNTRY,
                    ),
                    array(
                        'label' => $this->__('State'),
                        'value' => Epicor_Lists_Model_List_Address_Restriction::TYPE_STATE,
                    ),
                    array(
                        'label' => $this->__('Zip'),
                        'value' => Epicor_Lists_Model_List_Address_Restriction::TYPE_ZIP,
                    ),
                ),
            ))
            ->setAfterElementHtml('<input type="hidden" value="' . Mage::helper("adminhtml")->getUrl("adminhtml/epicorlists_list/restrictionsessionset/", array()) . '" name="ajax_url" id="ajax_url" /> <input type="hidden" value="' . Mage::helper("adminhtml")->getUrl("adminhtml/epicorlists_list/addupdate/", array()) . '" name="form_url" id="form_url" /> <input type="hidden" value="' . Mage::helper("adminhtml")->getUrl("adminhtml/epicorlists_list/addressdelete") . '" name="delete_url" id="delete_url" />');
        $selectedPurchaseType = $formData['restriction_type'];
        if ($selectedPurchaseType) {
            Mage::getSingleton('admin/session')->setPurchaseTypeValue($selectedPurchaseType);
        } else {
            Mage::getSingleton('admin/session')->setPurchaseTypeValue('');
        }
        $form->setValues($formData);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
