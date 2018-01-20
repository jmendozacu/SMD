<?php

/**
 * ERP Image type list renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Renderer_Erpimages_Types extends Mage_Adminhtml_Block_Abstract {

    private $_types = array(
        'L' => 'Large',
        'T' => 'Thumbnail',
        'S' => 'Small',
        'G' => 'Gallery'
    );

    public function _toHtml() {
        $html = parent::_toHtml();

        $typeNames = array();

        $types = $this->getRowData()->getTypes();

        if (!empty($types)) {
            $types = $types->getData();

            if (!empty($types)) {
                foreach ($types as $type) {
                    $typeNames[] = @$this->_types[$type];
                }
            }
        }

        $html .= implode(',', $typeNames);

        return $html;
    }

}
