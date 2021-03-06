<?php

/**
 * RFQ line attachments grid
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Attachments_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        $rfq = Mage::registry('current_rfq_row');
        /* @var $rfq Epicor_Common_Model_Xmlvarien */

        $this->setId('rfq_line_attachments_' . $rfq->getUniqueId());
        $this->setClass('rfq_line_attachments');
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


        $attData = ($rfq->getAttachments()) ? $rfq->getAttachments()->getasarrayAttachment() : array();
        $attachments = array();

        // add a unique id so we have a html array key for these things
        foreach ($attData as $row) {
            $row->setUniqueId(uniqid());
            $attachments[] = $row;
        }

        $this->setCustomData($attachments);
    }

    protected function _getColumns()
    {
        $columns = array();

        if (Mage::registry('rfqs_editable') || Mage::registry('rfqs_editable_partial')) {
            $columns['delete'] = array(
                'header' => Mage::helper('customerconnect')->__('Delete'),
                'align' => 'center',
                'index' => 'delete',
                'type' => 'text',
                'width' => '50px',
                'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Attachments_Renderer_Delete(),
                'filter' => false
            );
        }

        $columns['description'] = array(
            'header' => Mage::helper('customerconnect')->__('Description'),
            'align' => 'left',
            'index' => 'description',
            'type' => 'text',
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Attachments_Renderer_Description(),
            'filter' => false
        );

        $columns['filename'] = array(
            'header' => Mage::helper('customerconnect')->__('Filename'),
            'align' => 'left',
            'index' => 'filename',
            'type' => 'text',
            'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Attachments_Renderer_Filename(),
            'filter' => false
        );

        return $columns;
    }

    public function _toHtml()
    {
        $html = parent::_toHtml();

        $rfq = Mage::registry('current_rfq_row');
        /* @var $rfq Epicor_Common_Model_Xmlvarien */

        $html .= '<div style="display:none">
            <table>
                <tr title="" class="line_attachment_row" id="line_attachment_row_template_' . $rfq->getUniqueId() . '">
                    <td class="a-center">
                        <input type="checkbox" name="" class="line_attachments_delete" />
                    </td>
                    <td class="a-left ">
                        <input type="text" class="line_attachments_description" value="" name="" />
                    </td>
                    <td class="a-left ">
                        <input type="file" class="line_attachments_filename" name="">
                    </td>
                </tr>
            </table>
        </div>';
        return $html;
    }

    public function getRowClass(Varien_Object $row)
    {
        $extra = Mage::registry('rfq_new') ? ' new' : '';
        return 'line_attachment_row' . $extra;
    }

    public function getRowUrl($row)
    {
        return null;
    }

}
