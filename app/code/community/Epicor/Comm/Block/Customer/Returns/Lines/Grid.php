<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Comm_Block_Customer_Returns_Lines_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->isReview()) {
            $this->setId('return_lines');
        } else {
            $this->setId('return_lines_review');
        }

        $this->setIdColumn('id');
        $this->setDefaultSort('number');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setMessageBase('epicor_comm');
        $this->setCustomColumns($this->_getColumns());
        $this->setKeepRowObjectType(true);
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        $this->setCacheDisabled(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setShowAll(true);

        $lines = array();

        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */

        if ($return) {
            $linesData = $return->getLines() ? : array();
            foreach ($linesData as $row) {
                $row->setUniqueId(uniqid());
                $row->setRowIdentifier('lines_' . $row->getUniqueId());

                $lines[] = $row;
            }
        }

        $this->setCustomData($lines);
    }

    public function _toHtml()
    {
        $html = parent::_toHtml();
        if (!$this->isReview()) {

            if (Mage::registry('current_return_line')) {
                Mage::unregister('current_return_line');
            }

            Mage::register('current_return_line', new Varien_Object());

            $block = $this->getLayout()->createBlock('epicor_comm/customer_returns_lines_attachments');
            /* @var $block Epicor_Comm_Block_Customer_Returns_Lines_Attachments */
            $select = '<select name="" class="return_line_returncode">';

            $customer = Mage::getSingleton('customer/session')->getCustomer();
            /* @var $customer Epicor_Comm_Model_Customer */
            $codes = $customer->getReturnReasonCodes();
            $select .= '<option value="">Please select</option>';
            foreach ($codes as $code => $description) {
                $select .= '<option value="' . $code . '">' . $description . '</option>';
            }
            $select .= '</select>';

            $helper = Mage::helper('epicor_comm/returns');
            /* @var $helper Epicor_Comm_Helper_Returns */

            if ($helper->checkConfigFlag('line_attachments')) {
                $expandCol = '<td class="a-left expand-row " style="cursor: pointer;">
                            <span id="return-line-attachments-" class="plus-minus">+</span>
                        </td>';
            } else {
                $expandCol = '';
            }
            $notesLength = Mage::getStoreConfig('epicor_comm_returns/notes/line_notes_length');
            $maxLength = $notesLength? 'maxLength='.$notesLength : '';
            $notesRequired = Mage::getStoreConfig('epicor_comm_returns/notes/line_notes_required');
            $html .= '
            <div style="display:none">
                <table>
                    <tr title="" class="lines_row" id="return_lines_row_template">
                        ' . $expandCol . '
                        <td class="a-left ">
                            <input type="checkbox" name="delete" class="return_line_delete" />
                        </td>
                        <td class="a-left ">
                            <span class="return_line_number"></span>
                            <input type="hidden" name="configured" value="" class="return_line_configured" />
                            <input type="hidden" name="source_type" value="" class="return_line_source_type" />
                            <input type="hidden" name="source_value" value="" class="return_line_source_value" />
                        </td>
                        <td class="a-left ">
                            <span class="return_sku"></span>
                            <input type="hidden" name="sku" value="" class="return_line_sku" />
                        </td>
                        <td class="a-left ">
                            <span class="return_uom"></span>
                            <input type="hidden" name="uom" value="" class="return_line_uom" />
                        </td>
                        <td class="a-left ">
                            <input type="text" class="return_line_quantity_returned" value="" name="quantity_returned" />
                            <input type="hidden" class="return_line_quantity_ordered" value="" name="quantity_ordered" />
                            <span class="return_line_quantity_ordered_label"></span>
                        </td>
                        <td class="a-left ">
                            ' . $select . '
                        </td>';
           if($notesRequired){        
                $html .= '<td class="a-center">
                            <textarea class="return_line_notes" '.$maxLength.' name="notes"></textarea>';
                if($notesLength){
                    $html .= '<div id="truncated_message_line_notes">max '.$notesLength.' chars</div>';
                }
                $html .= '</td>';
            }            
            $html .=    '<td class="a-left ">
                            <span class="return_line_source_label"></span>
                            <input type="hidden" name="source" value="" class="return_line_source" />
                            <input type="hidden" name="source_data" value="" class="return_line_source_data" />
                        </td>
                        <td class="a-left expand-content no-display last"></td>
                    </tr>
                    <tr class="lines_row attachment" id="return_line_attachments_row_template" style="display:none">
                        <td colspan="10" class="attachments-row">'
                . $block->toHtml()
                . '</td>
                    </tr>
                </table>
            </div>';
        }
        return $html;
    }

    protected function _getColumns()
    {
        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */

        $addAllowed = ($return) ? $return->isActionAllowed('Add') : true;

        $columns = array(
            'expand' => array(
                'header' => ' <img src="' . $this->getSkinUrl('epicor/comm/images/icon_paperclip.gif') . '" alt="Attachments" /> ',
                'align' => 'left',
                'index' => 'expand',
                'type' => 'text',
                'column_css_class' => "expand-row",
                'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Expand(),
                'filter' => false
            ),
            'delete' => array(
                'header' => Mage::helper('epicor_comm')->__('Delete'),
                'align' => 'left',
                'index' => 'delete',
                'type' => 'text',
                'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Delete(),
                'filter' => false,
            ),
            'entity_id' => array(
                'header' => Mage::helper('epicor_comm')->__('Line'),
                'align' => 'left',
                'index' => 'number',
                'type' => 'number',
                'sortable' => false,
                'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Number(),
            ),
            'product_code' => array(
                'header' => Mage::helper('epicor_comm')->__('SKU'),
                'align' => 'left',
                'index' => 'product_code',
                'type' => 'text',
                'sortable' => false,
            ),
            'unit_of_measure_code' => array(
                'header' => Mage::helper('epicor_comm')->__('UOM'),
                'align' => 'left',
                'index' => 'unit_of_measure_code',
                'type' => 'text',
                'sortable' => false,
            ),
            'qty' => array(
                'header' => Mage::helper('epicor_comm')->__('Qty'),
                'align' => 'left',
                'index' => 'qty',
                'type' => 'text',
                'sortable' => false,
                'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Qty(),
            ),
            'return_code' => array(
                'header' => Mage::helper('epicor_comm')->__('Return Code'),
                'align' => 'left',
                'index' => 'return_code',
                'type' => 'text',
                'sortable' => false,
                'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Returncode(),
            ),
        );

        if(Mage::getStoreConfig('epicor_comm_returns/notes/line_notes_required')){            
            $columns['notes_text'] = array(
                                        'header' => Mage::helper('epicor_comm')->__('Notes'),
                                        'align' => 'left',
                                        'index' => 'note_text',
                                        'type' => 'text',
                                        'sortable' => false,
                                        'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Notes(),
                                    );
        }
        $columns['source'] = array(
                                    'header' => Mage::helper('epicor_comm')->__('Source'),
                                    'align' => 'left',
                                    'index' => 'source',
                                    'type' => 'text',
                                    'sortable' => false,
                                    'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Source(),
                                );
        $columns['attachments'] = array(
                                    'header' => ($this->isReview()) ? Mage::helper('epicor_comm')->__('Attachments') : '',
                                    'align' => 'left',
                                    'index' => 'attachments',
                                    'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Attachments(),
                                    'type' => 'text',
                                    'filter' => false,
                                    'keep_data_format' => 1,
                                    'column_css_class' => (!$this->isReview()) ? 'no-display' : '',
                                    'header_css_class' => (!$this->isReview()) ? 'no-display' : ''
                                ); 
        
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        if (!$helper->checkConfigFlag('line_attachments')) {
            unset($columns['expand']);
            unset($columns['attachments']);
        }
        
        if ($this->isReview()) {
            unset($columns['expand']);
            unset($columns['delete']);
        } else {
            if (!$addAllowed) {
                unset($columns['delete']);
            }
        }
        
        return $columns;
    }

    public function getRowClass($row)
    {
        /* @var $row Epicor_Comm_Model_Customer_Return_Line */
        $class = 'lines_row';

        if ($row->getToBeDeleted() == 'Y' && $this->isReview()) {
            $class .= ' deleting';
        }
        return $class;
    }

    public function getRowUrl($row)
    {
        return null;
    }

    private function isReview()
    {
        return Mage::registry('review_display');
    }

}
