<?php

/**
 * List ERP Accounts Restricted address form
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Restrictions_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();

        $this->_title = $this->getTitleString();
    }

    /**
     * Returns a string for use in the title
     * 
     * @return string
     */
    public function getTitleString()
    {
        $restrictionType = $this->getRequest()->getParam('restrictionTypeValue');

        $buttonType = $this->getRequest()->getParam('buttonType');
        
        if ($buttonType == 'add') {
            $title = 'New ';
        } else {
            $title = 'Updating ';
        }

        switch ($restrictionType) {
            case Epicor_Lists_Model_List_Address_Restriction::TYPE_ADDRESS:
                $title .= 'Address Restriction';
                break;
            case Epicor_Lists_Model_List_Address_Restriction::TYPE_COUNTRY:
                $title .= 'Country Restriction';
                break;
            case Epicor_Lists_Model_List_Address_Restriction::TYPE_STATE:
                $title .= 'State Restriction';
                break;
            case Epicor_Lists_Model_List_Address_Restriction::TYPE_ZIP;
                $title .= 'Zip Restriction';
                break;
        }

        return $title;
    }

    /**
     * Gets the List for this tab
     *
     * @return Epicor_Lists_Model_List
     */
    public function getList()
    {
        if (!$this->list) {
            if (Mage::registry('list')) {
                $this->list = Mage::registry('list');
            } else {
                $this->list = Mage::getModel('epicor_lists/list')->load($this->getRequest()->getParam('list_id'));
            }
        }
        return $this->list;
    }

    /**
     * Gets the address for this form
     *
     * @return Epicor_Lists_Model_List_Address
     */
    public function getAddress()
    {
        if (!$this->address) {
            if (Mage::registry('address')) {
                $this->address = Mage::registry('address');
            } else {
                $this->address = Mage::getModel('epicor_lists/list_address')->load($this->getRequest()->getParam('address_id'));
            }
        }
        return $this->address;
    }

    /**
     * Builds restriction address form
     *
     * @return form
     */
    protected function _prepareForm()
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        $address = $this->getAddress();

        /* @var $list Epicor_Lists_Model_List */
        $restrictionType = $this->getRequest()->getParam('restrictionTypeValue');

        $buttonType = $this->getRequest()->getParam('buttonType');
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('restrictions_form', array('legend' => $this->getTitleString()));

        $fieldset->setHeaderBar(
            '<button title="' . $helper->__('Close') . '" type="button" class="scalable" onclick="closeRestrictionForm();"><span><span><span>' . $helper->__('Close') . '</span></span></span></button>'
        );

        $fieldset->addField('address_post_url', 'hidden', array(
            'name' => 'post_url',
            'value' => $this->getUrl('adminhtml/epicorlists_list/restrictedaddresspost')
        ));

        $fieldset->addField('restriction_type', 'hidden', array(
            'name' => 'restriction_type',
            'value' => $restrictionType
        ));

        $fieldset->addField('address_delete_url', 'hidden', array(
            'name' => 'delete_url',
            'value' => $this->getUrl('adminhtml/epicorlists_list/addressdelete')
        ));

        $fieldset->addField('list_id', 'hidden', array(
            'name' => 'list_id',
            'value' => $this->getList()->getId()
        ));

        $fieldset->addField('address_id', 'hidden', array(
            'name' => 'address_id',
            'value' => $address->getId()
        ));

        $this->addTypeFields($restrictionType, $form, $fieldset);
        
        $form->addValues($address->getData());

        if ($buttonType == 'add') {
            $fieldset->addField('addSubmit', 'submit', array(
                'value' => $this->__('Add'),
                'onclick' => "saveRestrictionAddress();return false;",
                'name' => 'addSubmit',
                'class' => 'form-button'
            ));
        } else {
            $fieldset->addField('updateSubmit', 'submit', array(
                'value' => $this->__('Update'),
                'onclick' => "saveRestrictionAddress(this);return false;",
                'name' => 'updateSubmit',
                'class' => 'form-button'
            ));
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Adds fields to the form for the restriction type
     * 
     * @param string $restrictionType
     * @param Varien_Data_Form $form
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     */
    protected function addTypeFields($restrictionType, $form, $fieldset)
    {
        switch ($restrictionType) {
            case Epicor_Lists_Model_List_Address_Restriction::TYPE_ADDRESS:
                $this->addAddressFields($fieldset);
                $this->addCountry($fieldset);
                $this->addCounty($form, $fieldset);
                $this->addPostcode($fieldset);
                
                break;
            case Epicor_Lists_Model_List_Address_Restriction::TYPE_COUNTRY:
                $this->addCountry($fieldset);
                break;
            case Epicor_Lists_Model_List_Address_Restriction::TYPE_STATE:
                $this->addCountry($fieldset);
                $this->addCounty($form, $fieldset);
                break;
            case Epicor_Lists_Model_List_Address_Restriction::TYPE_ZIP:
                $this->addCountry($fieldset);
                $this->addPostcode($fieldset);
                break;
        }
    }

    /**
     * Adds address fields to the form
     * 
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     */
    protected function addAddressFields($fieldset)
    {
        $address = $this->getAddress();

        $fieldset->addField('address_name', 'text', array(
            'label' => $this->__('Customer Name'),
            'required' => false,
            'name' => 'name',
            'value' => $address->getName(),
            'after_element_html' => '<small>Expected format: Firstname Lastname</small>'
        ));

        $fieldset->addField('address1', 'text', array(
            'label' => $this->__('Address 1'),
            'required' => false,
            'name' => 'address1',
            'value' => $address->getAddress1()
        ));

        $fieldset->addField('address2', 'text', array(
            'label' => $this->__('Address 2'),
            'required' => false,
            'name' => 'address2',
            'value' => $address->getAddress2()
        ));

        $fieldset->addField('address3', 'text', array(
            'label' => $this->__('Address 3'),
            'required' => false,
            'name' => 'address3',
            'value' => $address->getAddress3()
        ));

        $fieldset->addField('city', 'text', array(
            'label' => $this->__('City'),
            'required' => false,
            'name' => 'city',
            'value' => $address->getCity()
        ));
    }

    /**
     * Adds country field to the form
     * 
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     */
    protected function addCountry($fieldset)
    {
        $country = $fieldset->addField('country', 'select', array(
            'label' => $this->__('Country'),
            'required' => true,
            'name' => 'country',
            'values' => Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
            'class' => 'countries'
        ));

        /*
         * Add Ajax to the Country select box html output
         */
        $country->setAfterElementHtml("<script type=\"text/javascript\">
            //<![CDATA[
            new RegionUpdater('country', 'county', 'county_id', " . $this->helper('directory')->getRegionJson() . ", undefined, undefined);
            //]]>
        </script>");
    }

    /**
     * Adds county field to the form
     * 
     * @param Varien_Data_Form $form
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     */
    protected function addCounty($form, $fieldset)
    {
        $address = $this->getAddress();
        $county_id = new Varien_Data_Form_Element_Select(array(
            'label' => '',
            'required' => true,
            'name' => 'county_id',
            'no_display' => true,
        ));
        $county_id->setForm($form);
        $county_id->setId('county_id');

        $fieldset->addField('county', 'text', array(
            'label' => Mage::helper('epicor_common')->__('County'),
            'required' => true,
            'name' => 'county',
            'value' => $address->getCounty(),
            'after_element_html' => $county_id->getElementHtml()
        ));
    }

    /**
     * Adds postcode field to the form
     * 
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     */
    protected function addPostcode($fieldset)
    {
        $address = $this->getAddress();
        $fieldset->addField('Postcode', 'text', array(
            'label' => $this->__('Postcode'),
            'required' => false,
            'name' => 'postcode',
            'value' => $address->getPostcode()
        ));
    }

}
