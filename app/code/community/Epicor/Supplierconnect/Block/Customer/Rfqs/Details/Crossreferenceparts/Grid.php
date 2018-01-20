<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Crossreferenceparts_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        #    $this->setId('order_number');     
        $this->setId('rfq_cross_reference_parts');
        $this->setDefaultSort('manufacturer');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        $this->setMessageBase('supplierconnect');
        $this->setMessageType('surd');
        $this->setIdColumn('manufacturer');
        $this->setDataSubset('cross_reference_parts/cross_reference_part');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);


        $rfq = Mage::registry('supplier_connect_rfq_details');
        /* @var $order Epicor_Common_Model_Xmlvarien */
        $xrefData = (array) $rfq->getVarienDataArrayFromPath('cross_reference_parts/cross_reference_part');
        $xref = array();

        // add a unique id so we have a html array key for these things
        foreach ($xrefData as $row) {
            $row->setUniqueId(uniqid());
            $xref[] = $row;
        }

        $this->setCustomData($xref);
    }

    protected function _getColumns()
    {
        $columns = array();
        if (Mage::registry('rfq_editable')) {
            $columns['delete_option'] = array(
                'header' => Mage::helper('epicor_comm')->__("Delete"),
                'align' => 'center',
                'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Crossreferenceparts_Renderer_Delete(),
                'type' => 'checkbox',
                'sortable' => false,
                'width' => '40'
            );
        }

        $columns['manufacturer_code'] = array(
            'header' => Mage::helper('epicor_comm')->__('Manufacturer'),
            'align' => 'left',
            'index' => 'manufacturer_code',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Crossreferenceparts_Renderer_Manufacturer(),
            'type' => 'text',
            'sortable' => false
        );

        $columns['manufacturers_product_code'] = array(
            'header' => Mage::helper('epicor_comm')->__("Manufacturer's Part"),
            'align' => 'left',
            'index' => 'manufacturers_product_code',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Crossreferenceparts_Renderer_Manufacturersproductcode(),
            'type' => 'text',
            'sortable' => false
        );

        $columns['supplier_product_code'] = array(
            'header' => Mage::helper('epicor_comm')->__("Supplier's Part"),
            'align' => 'left',
            'index' => 'supplier_product_code',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Crossreferenceparts_Renderer_Supplierproductcode(),
            'type' => 'text',
            'sortable' => false
        );

        $columns['supplier_lead_days'] = array(
            'header' => Mage::helper('epicor_comm')->__("Supplier's Lead Days"),
            'align' => 'left',
            'index' => 'supplier_lead_days',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Crossreferenceparts_Renderer_Supplierleaddays(),
            'type' => 'text',
            'sortable' => false
        );

        $columns['supplier_reference'] = array(
            'header' => Mage::helper('epicor_comm')->__("Supplier Reference"),
            'align' => 'left',
            'index' => 'supplier_reference',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Crossreferenceparts_Renderer_Supplierreference(),
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

        $rfq = Mage::registry('supplier_connect_rfq_details');
        $manufacturerSelect = '<select class="cross_reference_part_manufacturer" name="template_cross_reference_parts[existing][][manufacturer_code]">';
        $manufacturerSelect .= '<option value=""></option>';
        $productCodeSelect = '';

        if ($rfq->getCrossReferenceManufacturers()) {
            foreach ($rfq->getCrossReferenceManufacturers()->getasarrayCrossReferenceManufacturer() as $x => $manufacturer) {
                $manufacturerSelect .= '<option value="' . $manufacturer->getManufacturerCode() . '">' . $manufacturer->getDescription() . '</option>';
                $productCodeSelect .= '<select id="manufacturer_product_codes_' . $manufacturer->getManufacturerCode() . '" class="cross_reference_part_manufacturers_product_code" name="template_cross_reference_parts[existing][][manufacturers_product_code]">';
                if ($manufacturer->getManufactureParts()) {
                    foreach ($manufacturer->getManufactureParts()->getasarrayProductCode() as $productCode) {
                        $productCodeSelect .= '<option value="' . $productCode . '">' . $productCode . '</option>';
                    }
                }
                $productCodeSelect .= '</select>';
            }
        }

        $manufacturerSelect .= '</select>';

        $html .= '<div style="display:none">
            ' . $productCodeSelect . '
            <table>
            <tr title="" class="xref_row" id="cross_reference_parts_row_template">
                <td class="a-center">
                    <input type="checkbox" name="template_cross_reference_parts[][delete]" class="cross_reference_part_delete" />
                </td>
                <td class="a-left ">
                    ' . $manufacturerSelect . '
                </td>
                <td class="a-left ">
                    <select name="template_cross_reference_parts[][manufacturers_product_code]" class="cross_reference_part_manufacturers_product_code">
                        <option value=""></option>
                    </select>
                </td>
                <td class="a-left ">
                    <input type="text" value="" name="template_cross_reference_parts[][supplier_product_code]" class="cross_reference_part_supplier_product_code">
                </td>
                <td class="a-left ">
                    <input type="text" value="" name="template_cross_reference_parts[][supplier_lead_days]" class="cross_reference_part_supplier_lead_days">
                </td>
                <td class="a-left last">
                    <input type="text" value="" name="template_cross_reference_parts[][supplier_reference]" class="cross_reference_part_supplier_reference">
                </td>
            </tr>
            </table>
        </div>';
        $html .= '</script>';
        return $html;
    }

    public function getRowClass(Varien_Object $row)
    {
        return 'xref_row';
    }

}
