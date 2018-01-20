<?php

class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Contacts_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    private $_contactCodes = array();

    public function __construct()
    {
        parent::__construct();

        $this->setId('rfq_contacts');
        $this->setDefaultSort('product_code');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);

        $this->setMessageBase('customerconnect');
        $this->setMessageType('crqd');
        $this->setIdColumn('product_code');

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);

        $rfq = Mage::registry('customer_connect_rfq_details');
        /* @var $rfq Epicor_Common_Model_Xmlvarien */
        $contactsData = ($rfq->getContacts()) ? $rfq->getContacts()->getasarrayContact() : array();

        $contacts = array();

        // add a unique id so we have a html array key for these things
        foreach ($contactsData as $row) {
            $row->setUniqueId(uniqid());
            $this->_contactCodes[] = $row->getNumber();
            $contacts[] = $row;
        }

        $this->setCustomData($contacts);
    }

    protected function _getColumns()
    {

        $columns = array();

        $columns['delete'] = array(
            'header' => Mage::helper('customerconnect')->__('Delete'),
            'align' => 'center',
            'index' => 'delete',
            'type' => 'text',
            'width' => '50px',
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Contacts_Renderer_Delete(),
            'filter' => false,
            'column_css_class' => Mage::registry('rfqs_editable') ? '' : 'no-display',
            'header_css_class' => Mage::registry('rfqs_editable') ? '' : 'no-display',
        );

        $columns['name'] = array(
            'header' => Mage::helper('customerconnect')->__('Name'),
            'align' => 'left',
            'index' => 'name',
            'type' => 'text',
            'filter' => false
        );

        return $columns;
    }

    public function _toHtml()
    {
        $html = parent::_toHtml();

        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */

        $erpAccount = $helper->getErpAccountInfo();
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */

        $customerOptions = '';

        foreach ($erpAccount->getCustomers($erpAccount->getId()) as $customer) {
            /* @var $customer Epicor_Comm_Model_Customer */

            $code = $customer->getContactCode();

            if (!empty($code) && !in_array($code, $this->_contactCodes) || Mage::registry('rfq_new')) {

                if ($customer->getContactCode()) {
                    $details = array(
                        'number' => $customer->getContactCode(),
                        'name' => $customer->getName()
                    );

                    $customerOptions .= '<option value="' . base64_encode(serialize($details)) . '">'
                        . $customer->getName()
                        . '</option>';
                }
            }
        }

        $html .= '<div style="display:none">
            <table>
                <tr title="" class="contacts_row" id="contacts_row_template">
                    <td class="a-center">
                        <input type="checkbox" name="contacts[][delete]" class="contacts_delete"/>
                    </td>
                    <td class="a-left last">
                        <select name="contacts[][details]" class="contacts_details" >
                        ' . $customerOptions . '
                        </select>
                    </td>
                </tr>
            </table>
        </div>';




        $html .= '</script>';
        return $html;
    }

    public function getRowClass(Varien_Object $row)
    {
        $extra = Mage::registry('rfq_new') ? ' new' : '';
        return 'contacts_row' . $extra;
    }

    public function getRowUrl($row)
    {
        return null;
    }

}
