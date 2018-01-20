<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_New_Tab_Erpinfo extends Epicor_Common_Block_Adminhtml_Form_Abstract
{

    public function __construct()
    {
        parent::_construct();
        $this->_title = 'Details';
    }

    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('details', array('legend' => Mage::helper('epicor_comm')->__('Account Details')));

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('epicor_comm')->__('Name'),
            'required' => true,
            'name' => 'name'
        ));

        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('epicor_comm')->__('Email'),
            'required' => true,
            'name' => 'email',
            'class' => 'validate-email',
        ));
        
        $fieldset->addField(
            'store', 
            'select', 
            array(
                'label' => Mage::helper('epicor_comm')->__('Store'),
                'required' => true,
                'values' => $this->_getStores(),
                'name' => 'store'
            )
        );
        
        $data = Mage::getSingleton('adminhtml/session')->getFormData();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    /**
     * Gets an array of visible stores, for display in a select box (optgroup nested)
     * 
     * @return array - array of stores
     */
    private function _getStores()
    {
        $storeModel = Mage::getSingleton('adminhtml/system_store');
        /* @var $storeModel Mage_Adminhtml_Model_System_Store */

        $options = array();

        foreach ($storeModel->getWebsiteCollection() as $website) {
            /* @var $website Mage_Core_Model_Website */
            $websiteShow = false;
            $groupOptions = array();

            foreach ($storeModel->getGroupCollection() as $group) {
                /* @var $group Mage_Core_Model_Store_Group */
                if ($group->getWebsiteId() != $website->getId()) {
                    continue;
                }

                $websiteShow = true;
                $groupOptions[] = array(
                    'label' => $group->getName(),
                    'value' => 'store_' . $group->getDefaultStoreId()
                );
            }

            if ($websiteShow) {
                $options[] = array(
                    'label' => $website->getName(),
                    'value' => 'website_' . $website->getId()
                );

                if (!empty($groupOptions)) {
                    $options[] = array(
                        'label' => 'Store Groups',
                        'value' => $groupOptions
                    );
                }
            }
        }

        return $options;
    }

}
