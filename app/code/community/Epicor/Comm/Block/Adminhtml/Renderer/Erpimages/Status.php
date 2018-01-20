<?php

/**
 * ERP Image status renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Renderer_Erpimages_Status extends Mage_Adminhtml_Block_Abstract {

    public function _toHtml() {
        $html = parent::_toHtml();

        $rawStatus = $this->getRowData()->getStatus();
        $status = '';
        if ($rawStatus == '0') {
            $status = 'Not-Synced';
        } else if ($rawStatus == '1') {
            $status = 'Synced';
        } else {
            $status = 'Error: ' . $rawStatus;
        }

        $html .= $status;

        return $html;
    }

}
