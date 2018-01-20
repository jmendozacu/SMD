<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Lists_Block_Customer_Account_Contracts_Shippingaddress_List_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    private $_allowEdit;
    private $_allowDelete;

    public function __construct()
    {
        parent::__construct();

        $this->setId('customer_contracts_shippingaddress_list');
        $this->setIdColumn('id');
        $this->setDefaultSort('name');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setMessageBase('epicor_lists');
        $this->setCustomColumns($this->_getColumns());
        $this->setCacheDisabled(true);
        $this->setUseAjax(true);
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);

        $details = Mage::registry('epicor_lists_contracts_details');

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        if ($details) {
            $shippingAddresses = $details->getContract()->getVarienDataArrayFromPath('delivery_addresses/delivery_address');
            $this->setCustomData($shippingAddresses);
        } else {
            $this->setCustomColumns(array());
            $this->setCustomData(array());
        }
    }

    protected function _getColumns()
    {
        $columns = array(
            'name' => array(
                'header' => Mage::helper('epicor_lists')->__('Name'),
                'align' => 'left',
                'index' => 'name',
                'width' => '100px',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'address1' => array(
                'header' => Mage::helper('epicor_lists')->__('Address1'),
                'align' => 'left',
                'index' => 'address1',
                'width' => '150px',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'address2' => array(
                'header' => Mage::helper('epicor_lists')->__('Address2'),
                'align' => 'left',
                'index' => 'address2',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'address3' => array(
                'header' => Mage::helper('epicor_lists')->__('Address3'),
                'align' => 'left',
                'index' => 'address3',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'city' => array(
                'header' => Mage::helper('epicor_lists')->__('City'),
                'align' => 'left',
                'index' => 'city',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'postcode' => array(
                'header' => Mage::helper('epicor_lists')->__('Postcode'),
                'align' => 'left',
                'index' => 'postcode',
                'type' => 'postcode',
                'condition' => 'LIKE'
            ),
            'country' => array(
                'header' => Mage::helper('epicor_lists')->__('Country'),
                'align' => 'left',
                'index' => 'country',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'county' => array(
                'header' => Mage::helper('epicor_lists')->__('State/Province'),
                'align' => 'left',
                'index' => 'county',
                'condition' => 'LIKE',
                'type' => 'state',
            ),
        );

        return $columns;
    }

    public function getRowUrl($row)
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/grid/shippingsearch', array('_current' => true));
    }

}
