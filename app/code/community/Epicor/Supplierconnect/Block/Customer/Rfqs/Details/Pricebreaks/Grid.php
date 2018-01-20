<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Pricebreaks_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('rfq_price_breaks');
        $this->setDefaultSort('manufacturer');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        $this->setMessageBase('supplierconnect');
        $this->setMessageType('surd');
        $this->setIdColumn('price_break_code');
        $this->setDataSubset('price_breaks/price_break');
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);
        $rfq = Mage::registry('supplier_connect_rfq_details');
        /* @var $rfq Epicor_Common_Model_Xmlvarien */
        $this->setCustomData((array) $rfq->getVarienDataArrayFromPath('price_breaks/price_break'));
    }

    protected function _getColumns()
    {

        $columns = array();

        if (Mage::registry('rfq_editable')) {
            $columns['delete_option'] = array(
                'header' => Mage::helper('epicor_comm')->__("Delete"),
                'align' => 'center',
                'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Pricebreaks_Renderer_Delete(),
                'type' => 'checkbox',
                'sortable' => false,
                'width' => '40'
            );
        }

        $columns['quantity'] = array(
            'header' => Mage::helper('epicor_comm')->__("Minimum Qty"),
            'align' => 'left',
            'index' => 'quantity',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Pricebreaks_Renderer_Quantity(),
            'type' => 'input',
            'sortable' => false
        );
        
        $columns['days_out'] = array(
            'header' => Mage::helper('epicor_comm')->__("Days Out"),
            'align' => 'left',
            'index' => 'days_out',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Pricebreaks_Renderer_Daysout(),
            'type' => 'input',
            'sortable' => false
        );

        $columns['modifier'] = array(
            'header' => Mage::helper('epicor_comm')->__("Modifier"),
            'align' => 'left',
            'index' => 'modifier',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Pricebreaks_Renderer_Modifier(),
            'type' => 'input',
            'sortable' => false
        );

        $columns['effective_price'] = array(
            'header' => Mage::helper('epicor_comm')->__("Effective Price"),
            'align' => 'left',
            'index' => 'effective_price',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Pricebreaks_Renderer_EffectivePrice(),
            'type' => 'input',
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
            <tr title="" class="qpb_row" id="price_break_row_template">
                <td class="a-center">
                    <input type="checkbox" value="1" name="template_price_breaks[][delete]" class="price_break_delete"/>
                </td>
                <td class="a-left">
                    <input type="text" value="" name="template_price_breaks[][quantity]" class="price_break_min_quantity"/>
                </td>
                <td class="a-left">
                    <input type="text" value="" name="template_price_breaks[][days_out]" class="price_break_days_out"/>
                </td>
                <td class="a-left ">
                    <input type="text" value="" name="template_price_breaks[][modifier]" class="price_break_modifier"/>
                </td>
                <td class="a-left last">
                    <input type="hidden" value="" name="template_price_breaks[][effective_price]" class="price_break_effective_price"/>
                    <span class="price_break_effective_price_label"></span>
                </td>
            </tr> 
            </table>
        </div>';
        $html .= '</script>';
        return $html;
    }

    public function getRowClass(Varien_Object $row)
    {
        return 'qpb_row';
    }
}
