<?php

/**
 * RFQ lines grid 
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('rfq_lines');
        $this->setDefaultSort('_attributes_number');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(false);

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
        $this->getLineData();
    }

    protected function getLineData()
    {
        $rfq = Mage::registry('customer_connect_rfq_details');
        /* @var $rfq Epicor_Common_Model_Xmlvarien */
        $linesData = ($rfq->getLines()) ? $rfq->getLines()->getasarrayLine() : array();

        $lines = array();

        $editable = Mage::registry('rfqs_editable');
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */

        // add a unique id so we have a html array key for these things
        foreach ($linesData as $row) {
            $row->setUniqueId(uniqid());
            $row->setRowIdentifier('lines_' . $row->getUniqueId());
            $product = $row->getProduct();
            if ($editable && empty($product)) {
                $productCode = (string) $row->getData('product_code');
                $productUom = $row->getData('unit_of_measure_code');
                $rowProduct = $helper->findProductBySku($productCode, $productUom, false);
                if (empty($rowProduct) || !$rowProduct instanceof Epicor_Comm_Model_Product) {
                    $rowProduct = Mage::getModel('catalog/product');
                    $rowProduct->setSku($productCode);
                    $rowProduct->setUom($productUom);
                }

                $rowProduct->setQty($row->getQuantity());
                $rowProduct->setRfqLineId($row->getUniqueId());
                $row->setProduct($rowProduct);
            }

            $lines[] = $row;
        }

        $lineData = new Varien_Object($lines);
        Mage::dispatchEvent('epicor_customerconnect_crq_detail_lines_get_data_after', array(
            'block' => $this,
            'lines' => $lineData,
                )
        );

        $this->setCustomData($lineData->getData());
    }

    protected function _getColumns()
    {
        $columns = array();

        $columns['expand'] = array(
            'header' => Mage::helper('customerconnect')->__(''),
            'align' => 'left',
            'index' => 'expand',
            'type' => 'text',
            'column_css_class' => "expand-row",
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Expand(),
            'filter' => false,
            'sortable' => false
        );

        $columns['is_kit'] = array(
            'header' => Mage::helper('customerconnect')->__('Kit'),
            'align' => 'left',
            'index' => 'is_kit',
            'type' => 'text',
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Iskit(),
            'filter' => false,
            'sortable' => false
        );

        $columns['product_code'] = array(
            'header' => Mage::helper('customerconnect')->__('Part Number'),
            'align' => 'left',
            'index' => 'product_code',
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Partnumber(),
            'filter' => false,
            'sortable' => false
        );

        $columns['unit_of_measure_code'] = array(
            'header' => Mage::helper('customerconnect')->__('UOM'),
            'align' => 'left',
            'index' => 'unit_of_measure_code',
            'width' => '50px',
            'type' => 'text',
            'filter' => false,
            'sortable' => false
        );

        $columns['description'] = array(
            'header' => Mage::helper('customerconnect')->__('Description'),
            'align' => 'left',
            'index' => 'description',
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Description(),
            'type' => 'text',
            'filter' => false,
            'sortable' => false
        );


        $columns['price'] = array(
            'header' => Mage::helper('customerconnect')->__('Price'),
            'align' => 'right',
            'index' => 'price',
            'type' => 'number',
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Currency(),
            'filter' => false,
            'sortable' => false
        );

        $columns['quantity'] = array(
            'header' => Mage::helper('customerconnect')->__('Qty'),
            'align' => 'center',
            'index' => 'quantity',
            'type' => 'number',
            'value_format' => 'int',
            'width' => '60px',
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Qty(),
            'filter' => false,
            'sortable' => false,
        );

        $columns['rquest_date'] = array(
            'header' => Mage::helper('customerconnect')->__('Request Date'),
            'align' => 'left',
            'index' => 'request_date',
            'width' => '60px',
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Date(),
            'type' => 'text',
            'filter' => false,
            'sortable' => false
        );

        $columns['line_value'] = array(
            'header' => Mage::helper('customerconnect')->__('Total Price'),
            'align' => 'right',
            'index' => 'line_value',
            'type' => 'number',
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Currency(),
            'filter' => false,
            'sortable' => false
        );

        $columns['additional_text'] = array(
            'header' => Mage::helper('customerconnect')->__('Line Comments'),
            'align' => 'left',
            'index' => 'additional_text',
            'type' => 'text',
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Linecomments(),
            'sortable' => false
        );

        $columns['_attributes_number'] = array(
            'header' => Mage::helper('customerconnect')->__('Number'),
            'align' => 'left',
            'index' => '_attributes_number',
            'type' => 'number',
            'filter' => false,
            'sortable' => false,
            'column_css_class' => "no-display",
            'header_css_class' => "no-display",
        );

        $columns['select'] = array(
            'header' => Mage::helper('customerconnect')->__('Select'),
            'align' => 'center',
            'index' => 'delete',
            'type' => 'text',
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Select(),
            'filter' => false,
            'sortable' => false,
            'column_css_class' => Mage::registry('rfqs_editable') ? '' : 'no-display',
            'header_css_class' => Mage::registry('rfqs_editable') ? '' : 'no-display',
        );

        $columns['attachments'] = array(
            'header' => Mage::helper('customerconnect')->__(''),
            'align' => 'left',
            'index' => 'attachments',
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Attachments(),
            'type' => 'text',
            'filter' => false,
            'keep_data_format' => 1,
            'column_css_class' => "expand-content",
            'header_css_class' => "expand-content",
            'sortable' => false
        );

        $cols = new Varien_Object($columns);
        Mage::dispatchEvent('epicor_customerconnect_crq_detail_lines_grid_columns_after', array(
            'block' => $this,
            'columns' => $cols
                )
        );

        return $cols->getData();
    }

    public function _toHtml()
    {
        $html = parent::_toHtml();

        $rfq = Mage::registry('customer_connect_rfq_details');
        /* @var $rfq Epicor_Common_Model_Xmlvarien */

        if (Mage::registry('current_rfq_row')) {
            Mage::unregister('current_rfq_row');
        }

        Mage::register('current_rfq_row', new Varien_Object());

        $block = $this->getLayout()->createBlock('customerconnect/customer_rfqs_details_lines_attachments');
        /* @var $block Epicor_Customerconnect_Block_Customer_Rfqs_Details_Attachments */
        $attachment_support = Mage::getStoreConfig('customerconnect_enabled_messages/CRQD_request/attachment_support');
        if ($attachment_support) {
            $expand = '<td class="a-left expand-row " style="cursor: pointer;"><span id="attachments-" class="plus-minus">+</span>
                     </td>';
        } else {
            $expand = '<td class="" style=><span id="attachments-" class="plus-minus"></span>
                     </td>';
        }
        $html .= '<div style="display:none">
            <table>
            <tr title="" class="lines_row" id="lines_row_template">' . $expand . '
                <td class="a-left ">
                    <span class="is_kit_display"></span>
                    <input class="lines_is_kit" type="hidden" name="" value="" />
                    </td>
                <td class="a-left ">
                    <input type="hidden" class="lines_product_code" value="" name="" />
                    <input type="hidden" class="lines_type" value="" name="" />
                    <span class="product_code_display"></span>
                </td>
                <td class="a-left ">
                    <input type="hidden" class="lines_unit_of_measure_code" value="" name="" />
                    <span class="uom_display"></span>
                </td>
                <td class="a-left ">
                    <input type="text" class="lines_description" value="" name="" />
                    <span class="description_display"></span>
                </td>
                <td class="a-right ">
                    <input type="hidden" class="lines_price" value="" name="" />
                    <span class="lines_price_display"></span>
                </td>
                <td class="a-center ">
                    <input type="text" class="lines_quantity" value="" name="" />
                </td>
                <td class="a-left ">
                    <input type="text" class="lines_request_date" value="" name="" id="_request_date" />
                </td>
                <td class="a-right ">
                    <input type="hidden" class="lines_line_value" value="" name="" />
                    <span class="lines_line_value_display"></span>
                </td>
                <td class="a-left ">
                    <textarea class="lines_additional_text"  name=""></textarea>
                </td>
                <td class="a-center ">
                    <input type="checkbox" name="" class="lines_select" />
                    <input type="hidden" name="" class="lines_product_json" />
                    <input type="hidden" name="" class="lines_delete" />
                    <input type="hidden" name="" class="lines_orig_quantity" />
                    <input type="hidden" name="" class="lines_uom" />
                    <input type="hidden" name="" class="lines_group_sequence" />
                    <input type="hidden" name="" class="lines_ewa_code" />
                    <input type="hidden" name="" class="lines_ewa_title" />
                    <input type="hidden" name="" class="lines_ewa_sku" />
                    <input type="hidden" name="" class="lines_ewa_short_description" />
                    <input type="hidden" name="" class="lines_ewa_description" />
                    <input type="hidden" name="" class="lines_configured" />
                    <input type="hidden" name="" class="lines_attributes" />
                    <input type="hidden" name="" class="lines_product_id" />
                    <input type="hidden" name="" class="lines_child_id" />
                    <input type="hidden" name="" class="lines_configured" />
                </td>
                <td class="a-left expand-content last"></td>
            </tr>
            <tr class="lines_row attachment" id="line_attachments_row_template" style="display:none">
                <td colspan="12" class="shipping-row">'
                . $block->toHtml()
                . '</td>
            </tr>
            </table>
        </div>';
        $html .= '</script>';
        return $html;
    }

    public function getRowClass(Varien_Object $row)
    {
        $extra = Mage::registry('rfq_new') ? ' new' : '';
        return 'lines_row' . $extra;
    }

    public function getRowUrl($row)
    {
        return null;
    }

}
