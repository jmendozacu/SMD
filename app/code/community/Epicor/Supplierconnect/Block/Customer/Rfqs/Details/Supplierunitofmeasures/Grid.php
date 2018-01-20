<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Supplierunitofmeasures_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('rfq_supplier_unit_of_measures');
        $this->setDefaultSort('manufacturer');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        $this->setMessageBase('supplierconnect');
        $this->setMessageType('surd');
        $this->setIdColumn('supplier_uom');
        $this->setDataSubset('supplier_unit_of_measures/supplier_unit_of_measure');
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);
        $rfq = Mage::registry('supplier_connect_rfq_details');
        /* @var $rfq Epicor_Common_Model_Xmlvarien */
        $this->setCustomData((array) $rfq->getVarienDataArrayFromPath('supplier_unit_of_measures/supplier_unit_of_measure'));
    }

    protected function _getColumns()
    {

        $columns = array();
        if (Mage::registry('rfq_editable') && Mage::registry('allow_conversion_override')) {
            $columns['delete_option'] = array(
                'header' => Mage::helper('epicor_comm')->__("Delete"),
                'align' => 'center',
                'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Supplierunitofmeasures_Renderer_Delete(),
                'type' => 'checkbox',
                'sortable' => false,
                'width' => '40'
            );
        }

        $columns['unit_of_measure'] = array(
            'header' => Mage::helper('supplierconnect')->__("Unit_Of_Measure"),
            'align' => 'left',
            'index' => 'unit_of_measure',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Supplierunitofmeasures_Renderer_UnitOfMeasure(),
            'type' => 'text',
            'sortable' => false
        );
        $columns['conversion_factor'] = array(
            'header' => Mage::helper('supplierconnect')->__("Conversion Factor"),
            'align' => 'left',
            'index' => 'conversion_factor',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Supplierunitofmeasures_Renderer_ConversionFactor(),
            'type' => 'text',
            'sortable' => false
        );
        $columns['operator'] = array(
            'header' => Mage::helper('supplierconnect')->__("Operator"),
            'align' => 'left',
            'index' => 'operator',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Supplierunitofmeasures_Renderer_Operator(),
            'type' => 'text',
            'sortable' => false
        );
        $columns['value'] = array(
            'header' => Mage::helper('supplierconnect')->__("Value"),
            'align' => 'left',
            'index' => 'value',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Supplierunitofmeasures_Renderer_Value(),
            'type' => 'number',
            'sortable' => false
        );
        $columns['result'] = array(
            'header' => Mage::helper('supplierconnect')->__("Result"),
            'align' => 'left',
            'index' => 'result',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Supplierunitofmeasures_Renderer_Result(),
            'type' => 'text',
            'sortable' => false
        );

        return $columns;
    }

    public function getRowUrl($row)
    {
        return null;
    }

    public function _toHtml()
    {
        $html = parent::_toHtml();
        $html .= '<div style="display:none">
            <table>
                <tr title="" class="suom_row" id="supplier_unit_of_measures_row_template">
                    <td class="a-center">
                        <input type="checkbox" value="1" name="template_supplier_unit_of_measures[][delete]" class="suom_delete"/>
                    </td>
                    <td class="a-left">
                        <input type="text" value="" name="template_supplier_unit_of_measures[][unit_of_measure]" class="suom_unit_of_measure"/>
                    </td>
                    <td class="a-left">
                        <input type="text" value="" name="supplier_unit_of_measures[][conversion_factor]" class="suom_conversion_factor"/>
                    </td>
                    <td class="a-left">
                        <select name="supplier_unit_of_measures[][operator]" class="suom_operator"><option value="*">Multiply</option><option value="/">Divide</option></select>
                    </td>
                    <td class="a-left">
                        <input type="text" value="" name="template_supplier_unit_of_measures[][value]" class="suom_value"/>
                    </td>
                    <td class="a-left last">
                        <input type="text" value="" name="template_supplier_unit_of_measures[][result]" class="suom_result"/>
                    </td>
                </tr>
            </table>
        </div>';
        $html .= '</script>';
        return $html;
    }

    public function getRowClass(Varien_Object $row)
    {
        return 'suom_row';
    }

}
