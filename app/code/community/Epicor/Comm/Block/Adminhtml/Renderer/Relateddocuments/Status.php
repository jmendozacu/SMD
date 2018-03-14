<?php

/**
 * ERP Image status renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Renderer_Relateddocuments_Status extends Mage_Adminhtml_Block_Abstract 
{
    protected $_rowId;
    
    public function __construct($rowData)
    {
        $this->_rowId = $rowData['row_id'];
        parent::__construct($rowData);
    }

    public function _toHtml() 
    {
        $html = parent::_toHtml();

        $rawStatus = $this->getRowData()->getSyncRequired();
        $syncRequired = '';
        if ($rawStatus == '1') {
            $syncRequired = 'Y';
        } else {
            $syncRequired = 'N';
        }

        $html .= $syncRequired . '<input type = "hidden" name = "product[related_documents]['.$this->_rowId.'][sync_required]" value = "' . $rawStatus . '" />';

        return $html;
    }

}
