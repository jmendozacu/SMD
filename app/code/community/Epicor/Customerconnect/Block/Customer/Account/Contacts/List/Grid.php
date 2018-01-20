<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Customerconnect_Block_Customer_Account_Contacts_List_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    private $_allowEdit;
    private $_allowDelete;

    public function __construct()
    {
        parent::__construct();

        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        $this->_allowEdit = $helper->customerHasAccess('Epicor_Customerconnect', 'Account', 'saveContact', '', 'Access');
        $this->_allowDelete = $helper->customerHasAccess('Epicor_Customerconnect', 'Account', 'deleteContact', '', 'Access');
        if (!Mage::helper('customerconnect')->checkMsgAvailable('CUAU')) {
            $this->_allowEdit = false;
            $this->_allowDelete = false;
        }
        if ($this->_allowEdit) {
            $this->setRowClickCallback('editContact');
        }

        $this->setId('customer_account_contacts_list');
        $this->setIdColumn('id');
        $this->setDefaultSort('name');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setMessageBase('customerconnect');
        $this->setCustomColumns($this->_getColumns());
        $this->setCacheDisabled(true);
        $this->setUseAjax(true);
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        //      $this->setRowUrlValue('*/*/editContact');

        $details = Mage::registry('customer_connect_account_details');
        /* @var $order Epicor_Common_Model_Xmlvarien */

        if ($details) {
            $helper = Mage::helper('epicor_comm');
            /* @var $helper Epicor_Comm_Helper_Data */

            $customerhelper = Mage::helper("customerconnect");
            /* @var $customerhelper Epicor_Customerconnect_Helper_Data */
            $contacts = $details->getVarienDataArrayFromPath('contacts/contact');
//            $contactsFound = array();
            $contactEmails = array();
            foreach ($contacts as $x => $contact) {
                if ($contact->getLoginId()) {
                    $contact->setSource($customerhelper::SYNC_OPTION_ECC_ERP);
                } else {
                    $contact->setSource($customerhelper::SYNC_OPTION_ONLY_ERP);
                }
                $contactEmails[$x] = $contact->getEmailAddress();
                $customer = Mage::getModel('customer/customer');
                $customer->setWebsiteId(Mage::app()->getDefaultStoreView()->getWebsiteId());
                /* @var $customer Epicor_Comm_Model_Customer */

                if ($customer->loadByEmail($contact->getEmailAddress())) {
                    $contact->setMasterShopper($customer->getEccMasterShopper() ? 'y' : 'n');
                }
            }

            $erp_group = $helper->getErpAccountInfo();
            /* @var $erp_group Epicor_Comm_Model_Customer_Erpaccount */

            $customers = $erp_group->getCustomers()
                    ->addAttributeToFilter('website_id',Mage::app()->getWebsite()->getId())
                    ->addAttributeToSelect('ecc_master_shopper')->getItems();

            $customerEmails = array();
            foreach ($customers as $key => $customer) {
                /* @var $customer Mage_Customer_Model_Customer */

                if (!in_array($customer->getEmail(), $customerEmails)) {
                    $customerEmails[] = $customer->getEmail();
                    if (!in_array($customer->getEmail(), $contactEmails)) {
                        $eccContact = new Varien_Object();
                        $eccContact->setContact_code($customer->getContactCode());
                        $eccContact->setName($customer->getName());
                        $eccContact->setFunction($customer->getFunction());
                        $eccContact->setTelephone_number($customer->getTelephoneNumber());
                        $eccContact->setFax_number($customer->getFaxNumber());
                        $eccContact->setEmailAddress($customer->getEmail());
                        $eccContact->setLogin_id($customer->getLoginId());
                        $eccContact->setSource($customerhelper::SYNC_OPTION_ONLY_ECC); //"Only in ECC"
                        $eccContact->setMasterShopper($customer->getEccMasterShopper() ? 'y' : 'n');
                        $contacts[] = $eccContact;
                    } else {
                        $contactKey = array_search($customer->getEmail(), $contactEmails);
                        $contact = $contacts[$contactKey];
                        $customerVal = $customer->getEccMasterShopper() ? 'y' : 'n';
                        $contact->setMasterShopper($customerVal);
                    }
                }
            }

            $this->setCustomData($contacts);
        } else {
            $this->setCustomColumns(array());
            $this->setCustomData(array());
            $this->setFilterVisibility(false);
            $this->setPagerVisibility(false);
        }
    }

    protected function _getColumns()
    {

        $columns = array(
            'name' => array(
                'header' => Mage::helper('epicor_comm')->__('Name'),
                'align' => 'left',
                'index' => 'name',
                'width' => '100px',
                'renderer' => new Epicor_Customerconnect_Block_Customer_Account_Contacts_Renderer_Contacts(),
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'function' => array(
                'header' => Mage::helper('epicor_comm')->__('Function'),
                'align' => 'left',
                'index' => 'function',
                'width' => '50px',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'telephone_number' => array(
                'header' => Mage::helper('epicor_comm')->__('Telephone Number'),
                'align' => 'left',
                'index' => 'telephone_number',
                'width' => '50px',
                'type' => 'phone',
                'condition' => 'LIKE'
            ),
            'fax_number' => array(
                'header' => Mage::helper('epicor_comm')->__('Fax Number'),
                'align' => 'left',
                'index' => 'fax_number',
                'width' => '50px',
                'type' => 'phone',
                'condition' => 'LIKE'
            ),
            'email_address' => array(
                'header' => Mage::helper('epicor_comm')->__('Email Address'),
                'align' => 'left',
                'index' => 'email_address',
                'width' => '100px',
                'type' => 'email',
                'condition' => 'LIKE'
            ),
            'login_id' => array(
                'header' => Mage::helper('epicor_comm')->__('Web Enabled'),
                'align' => 'center',
                'index' => 'login_id',
                'width' => '75px',
                'type' => 'options',
                'options' => array(
                    'yes' => 'Yes',
                    'no' => 'No'
                ),
                'tick_mode' => 'content',
                'renderer' => new Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Tickcross(),
                'filter_condition_callback' => array($this, 'filterWebEnabledCallback')
            ),
            'master_shopper' => array(
                'header' => Mage::helper('epicor_comm')->__('Master Shopper'),
                'align' => 'center',
                'index' => 'master_shopper',
                'width' => '75px',
                'type' => 'options',
                'options' => array(
                    'y' => Mage::helper('epicor_comm')->__('Yes'),
                    'n' => Mage::helper('epicor_comm')->__('No'),
                // 'wyen' => Mage::helper('epicor_comm')->__('Web-Yes ERP-No'),
                //'wney' => Mage::helper('epicor_comm')->__('Web-No ERP-Yes'),
                ),
                'renderer' => new Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Mastershopper(),
            ),
            'source' => array(
                'header' => Mage::helper('epicor_comm')->__('ERP Web Enabled'),
                'align' => 'left',
                'index' => 'source',
                'width' => '100px',
                'renderer' => new Epicor_Customerconnect_Block_Customer_Account_Contacts_Renderer_Contactsource(),
                'type' => 'text',
                'condition' => 'LIKE'
            ),
        );

        if ($this->_allowEdit || $this->_allowDelete) {
            $columns['action'] = array(
                'header' => Mage::helper('customerconnect')->__('Action'),
                'width' => '25px',
                'type' => 'action',
                'action' => array(),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
                'renderer' => new Epicor_Customerconnect_Block_Customer_Account_Contacts_Renderer_Contactsync(),
            );

            if ($this->_allowEdit) {
                $columns['action']['actions'][] = array(
                    'caption' => Mage::helper('customerconnect')->__('Edit'),
                    'url' => "javascript:;",
                );
            }

            if ($this->_allowDelete) {
                $columns['action']['actions'][] = array(
                    'caption' => Mage::helper('customerconnect')->__('Delete'),
                    'confirm' => Mage::helper('customerconnect')->__('Are you sure you want to delete this contact?  This action cannot be undone.'),
                    'url' => "javascript:void(0);",
                );
            }
        }

        $columns['action']['actions'][] = array(
            'caption' => Mage::helper('customerconnect')->__('Sync Contact'),
            'url' => "javascript:;",
        );

        return $columns;
    }

    /**
     * Filters the web enabled column
     * 
     * @param Epicor_Common_Model_Message_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     */
    protected function filterWebEnabledCallback($collection, $column)
    {
        $filterValue = $column->getFilter()->getValue();
        if ($filterValue == 'yes') {
            $collection->addFilter('login_id', array('neq' => ''));
        } else {
            $collection->addFilter('login_id', array('eq' => ''));
        }
    }

    public function getRowUrl($row)
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/grid/contactssearch');
    }

}
