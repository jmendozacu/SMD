<?php

/**
 * ERP Image store list renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Renderer_Erpimages_Stores extends Mage_Adminhtml_Block_Abstract {

    public function _toHtml() {
        $html = parent::_toHtml();
        $stores = $this->getRowData()->getStores();
        if($stores) {
            $storeData = Mage::app()->getStores();
            $stores = $stores->getData();
            $storeInfo = $this->getRowData()->getStoreInfo()->getData();
            $storeList = array();

            $html .= '
                <table class="border">
                    <thead>
                        <tr class="headings">
                            <th>Store</th>
                            <th>Description</th>
                            <th>Position</th>
                            <th>Types</th>
                            <th>Source</th>
                        </tr>
                    </thead>
                    <tbody>
            ';

            foreach ($stores as $store) {
                if (isset($storeData[$store])) {
                    $typeRenderer = new Epicor_Comm_Block_Adminhtml_Renderer_Erpimages_Types(array('row_data' => $storeInfo[$store]));

                    $source = array();
                    if($storeInfo[$store]['STK']) {
                        $source[] = 'STK';
                    }
                    if($storeInfo[$store]['STT']) {
                        $source[] = 'STT';
                    }
                    
                    $html .= '
                        <tr>
                            <td>' . $storeData[$store]->getName() . '</td>
                            <td>' . $storeInfo[$store]['description'] . '</td>
                            <td>' . $storeInfo[$store]['position'] . '</td>
                            <td>' . $typeRenderer->_toHtml() . '</td>
                            <td>' . implode(',',$source) . '</td>
                        </tr>
                    ';
                }
            }

            $html .= '
                    </tbody>
                </table>
            ';

            $html .= implode('<br />', $storeList);
        }
        return $html;
    }

}
