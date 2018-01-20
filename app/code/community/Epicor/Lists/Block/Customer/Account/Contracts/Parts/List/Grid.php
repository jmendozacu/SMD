<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Lists_Block_Customer_Account_Contracts_Parts_List_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('customer_contracts_parts_list');
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
            $parts = $details->getContract()->getVarienDataArrayFromPath('parts/part');
            $this->setCustomData($parts);
        } else {
            $this->setCustomColumns(array());
            $this->setCustomData(array());
        }
    }

    protected function _getColumns()
    {
        $columns = array(
            'contract_line_number' => array(
                'header' => Mage::helper('epicor_lists')->__('Contract Line Number'),
                'align' => 'left',
                'index' => 'contract_line_number',
                'width' => '100px',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'contract_part_number' => array(
                'header' => Mage::helper('epicor_lists')->__('Contract Part Number'),
                'align' => 'left',
                'index' => 'contract_part_number',
                'width' => '100px',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'effective_start_date' => array(
                'header' => Mage::helper('epicor_lists')->__('Start Date'),
                'align' => 'left',
                'index' => 'effective_start_date',
                'width' => '50px',
                'type' => 'date',
                'condition' => 'LIKE'
            ),
            'effective_end_date' => array(
                'header' => Mage::helper('epicor_lists')->__('End Date'),
                'align' => 'left',
                'index' => 'effective_end_date',
                'width' => '50px',
                'type' => 'date',
                'condition' => 'LIKE'
            ),
            'line_status' => array(
                'header' => Mage::helper('epicor_lists')->__('Line Status'),
                'align' => 'left',
                'index' => 'line_status',
                'width' => '20px',
                'type' => 'text',
                'condition' => 'LIKE',
                'renderer' => new Epicor_Lists_Block_Customer_Account_Contracts_parts_List_Renderer_Status(),
            ),
            'product_code' => array(
                'header' => Mage::helper('epicor_lists')->__('Sku'),
                'align' => 'left',
                'index' => 'product_code',
                'width' => '20px',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'unit_of_measures' => array(
                'header' => Mage::helper('epicor_lists')->__('UOM'),
                'align' => 'center',
                'index' => 'unit_of_measures',
                'width' => '5px',
                'type' => 'text',
                'renderer' => new Epicor_Lists_Block_Customer_Account_Contracts_parts_List_Renderer_Uom(),
            ),
        );

        return $columns;
    }

    public function _toHtml()
    {
        $html = parent::_toHtml();


        $html .= '<script>
                $$("tr [id ^= \'part_uom\']").each(function(a){
  
                        a.up(\'td\').observe(\'click\', function() {
                                id = a.readAttribute(\'id\').split(\'part_uom_col_\');
                                productCode = id[1];
                                if(a.innerHTML == \'+\'){          
                                        a.innerHTML = \'-\'; 
                                        $("parts_row_uom_" + productCode).show();
                                }else{
                                        $("parts_row_uom_" + productCode).hide();
                                        a.innerHTML = \'+\'; 
                                }
                        });
                })</script>';
        return $html;
    }

    public function getRowUrl($row)
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/grid/partssearch', array('_current' => true));
    }

}
