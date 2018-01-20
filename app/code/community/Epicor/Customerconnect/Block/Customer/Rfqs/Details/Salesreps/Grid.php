<?php

/**
 * RFQ sales rep grid
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Salesreps_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('rfq_salesreps');
        $this->setDefaultSort('number');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);

        $this->setMessageBase('customerconnect');
        $this->setMessageType('crqd');
        $this->setIdColumn('number');

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);

        $rfq = Mage::registry('customer_connect_rfq_details');
        /* @var $rfq Epicor_Common_Model_Xmlvarien */
        $salesrepsData = ($rfq->getSalesReps()) ? $rfq->getSalesReps()->getasarraySalesRep() : array();
        $salesreps = array();

        // add a unique id so we have a html array key for these things
        foreach ($salesrepsData as $row) {
            $row->setUniqueId(uniqid());
            $salesreps[] = $row;
        }

        $this->setCustomData($salesreps);
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
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Salesreps_Renderer_Delete(),
            'filter' => false,
            'column_css_class' => Mage::registry('rfqs_editable') ? '' : 'no-display',
            'header_css_class' => Mage::registry('rfqs_editable') ? '' : 'no-display',
        );

        $columns['name'] = array(
            'header' => Mage::helper('customerconnect')->__('Name'),
            'align' => 'left',
            'index' => 'name',
            'type' => 'text',
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Salesreps_Renderer_Name(),
            'filter' => false
        );

        return $columns;
    }

    public function _toHtml()
    {
        $html = parent::_toHtml();

        $html .= '<div style="display:none">
            <table>
                <tr title="" class="salesreps_row" id="salesreps_row_template">
                    <td class="a-center">
                        <input type="checkbox" name="salesreps[][delete]" class="salesreps_delete"/>
                    </td>
                    <td class="a-left last">
                        <input type="text" value="" name="salesreps[][name]" class="salesreps_name" />
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
        return 'salesreps_row' . $extra;
    }

    public function getRowUrl($row)
    {
        return null;
    }

}
