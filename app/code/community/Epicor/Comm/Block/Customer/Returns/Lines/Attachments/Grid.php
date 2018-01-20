<?php

/**
 * Return line attachments grid
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Lines_Attachments_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        $line = Mage::registry('current_return_line');
        /* @var $line Epicor_Comm_Model_Customer_Return_Line */

        if (!Mage::registry('review_display')) {
            $this->setId('return_line_attachments_' . $line->getUniqueId());
        } else {
            $this->setId('return_line_attachments_' . $line->getUniqueId(). '_review');
        }
        $this->setClass('return_line_attachments');
        $this->setDefaultSort('number');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);

        $this->setMessageBase('epicor_comm');
        $this->setIdColumn('number');

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);

        $attData = $line->getAttachments() ? : array();

        $attachments = array();

        // add a unique id so we have a html array key for these things
        foreach ($attData as $row) {
            $attachment = Mage::getModel('epicor_common/file')->load($row->getAttachmentId());
            /* @var $attachment Epicor_Common_Model_File */
            $row->setUniqueId(uniqid());
            $row->setAttachmentModel($attachment);
            $attachments[] = $row;
        }

        $this->setCustomData($attachments);
    }

    protected function _getColumns()
    {
        $line = Mage::registry('current_return_line');
        /* @var $line Epicor_Comm_Model_Customer_Return_Line */

        $allowed = ($line instanceof Epicor_Comm_Model_Customer_Return_Line) ? $line->isActionAllowed('Attachments') : true;
        
        $columns = array(
            'delete' => array(
                'header' => Mage::helper('epicor_comm')->__('Delete'),
                'align' => 'center',
                'index' => 'delete',
                'type' => 'text',
                'width' => '50px',
                'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Attachments_Renderer_Delete(),
                'filter' => false,
                'sortable' => false,
                'column_css_class' => (!$allowed) ? 'no-display' : '',
                'header_css_class' => (!$allowed) ? 'no-display' : ''
            ),
            'description' => array(
                'header' => Mage::helper('epicor_comm')->__('Description'),
                'align' => 'left',
                'index' => 'description',
                'type' => 'text',
                'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Attachments_Renderer_Description(),
                'filter' => false,
                'sortable' => false
            ),
            'filename' => array(
                'header' => Mage::helper('epicor_comm')->__('Filename'),
                'align' => 'left',
                'index' => 'filename',
                'type' => 'text',
                'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Attachments_Renderer_File(),
                'filter' => false,
                'sortable' => false
            )
        );

        if (Mage::registry('review_display')) {
            unset($columns['delete']);
        }

        return $columns;
    }

    public function _toHtml()
    {
        $html = parent::_toHtml();

        if (!Mage::registry('review_display')) {
            $line = Mage::registry('current_return_line');
            /* @var $line Epicor_Comm_Model_Customer_Return_Line */

            $html .= '<div style="display:none">
                <table>
                    <tr title="" class="attachments_row" id="return_line_attachments_' . $line->getUniqueId() . '_attachment_row_template">
                        <td class="a-center">
                            <input type="checkbox" name="" class="attachments_delete" />
                        </td>
                        <td class="a-left ">
                            <input type="text" class="attachments_description" value="" name="" />
                        </td>
                        <td class="a-left ">
                            <input type="file" class="attachments_filename" name="">
                        </td>
                    </tr>
                </table>
            </div>';
        }
        return $html;
    }

    public function getRowClass(Varien_Object $row)
    {
        return 'attachments_row'; 
    }

    public function getRowUrl($row)
    {
        return null;
    }

}
