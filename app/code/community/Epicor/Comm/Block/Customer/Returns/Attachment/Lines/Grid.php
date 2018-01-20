<?php

class Epicor_Comm_Block_Customer_Returns_Attachment_Lines_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->isReview()) {
            $this->setId('customer_returns_attachment_lines');
        } else {
            $this->setId('return_attachments_review');
        }

        $this->setIdColumn('id');
        $this->setDefaultSort('filename');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setMessageBase('epicor_comm');
        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        $this->setCacheDisabled(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setShowAll(true);
        $this->setKeepRowObjectType(true);

        $attachments = array();

        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */

        if ($return) {
            $attData = $return->getAttachments() ? : array();
            foreach ($attData as $row) {
                $attachment = Mage::getModel('epicor_common/file')->load($row->getAttachmentId());
                /* @var $attachment Epicor_Common_Model_File */
                $row->setUniqueId(uniqid());
                $row->setAttachmentModel($attachment);
                $attachments[] = $row;
            }
        }

        $this->setCustomData($attachments);

        if ($this->isReview()) {
            $this->_emptyText = Mage::helper('adminhtml')->__('No Attachments added');
        }
    }

    protected function _getColumns()
    {
        $columns = array();

        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */
        $allowed = ($return) ? $return->isActionAllowed('Attachments') : true;
        
        if (!$this->isReview() && $allowed) {
            $columns['delete'] = array(
                'header' => Mage::helper('customerconnect')->__('Delete'),
                'align' => 'center',
                'index' => 'delete',
                'type' => 'text',
                'width' => '50px',
                'renderer' => new Epicor_Comm_Block_Customer_Returns_Attachment_Lines_Renderer_Delete(),
                'filter' => false,
                'sortable' => false,
            );
        }

        $columns['description'] = array(
            'header' => Mage::helper('customerconnect')->__('Description'),
            'align' => 'left',
            'index' => 'description',
            'type' => 'text',
            'renderer' => new Epicor_Comm_Block_Customer_Returns_Attachment_Lines_Renderer_Description(),
            'filter' => false,
            'sortable' => false,
        );

        $columns['filename'] = array(
            'header' => Mage::helper('customerconnect')->__('Filename'),
            'align' => 'left',
            'index' => 'filename',
            'type' => 'text',
            'renderer' => new Epicor_Comm_Block_Customer_Returns_Attachment_Lines_Renderer_File(),
            'filter' => false,
            'sortable' => false,
        );

        return $columns;
    }

    public function _toHtml()
    {
        $html = parent::_toHtml();

        if (!$this->isReview()) {
            $html .= '<div style="display:none">
            <table>
                <tr title="" class="attachments_row" id="customer_returns_attachment_lines_attachment_row_template">
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

    public function getRowClass($row)
    {
        /* @var $row Epicor_Common_Model_File */
        $class = 'attachments_row';

        if ($this->isReview()) {
            $return = Mage::registry('return_model');
            /* @var $return Epicor_Comm_Model_Customer_Return */
            $link = $return->getAttachmentLink($row->getId());
            /* @var $link Epicor_Comm_Model_Customer_Return_Attachment */
            if ($link && $link->getToBeDeleted() == 'Y') {
                $class .= ' deleting';
            }
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
