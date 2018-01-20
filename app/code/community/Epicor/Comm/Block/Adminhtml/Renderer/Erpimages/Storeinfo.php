<?php

class Epicor_Comm_Block_Adminhtml_Renderer_Erpimages_Types extends Mage_Adminhtml_Block_Abstract {

    private $_types = array(
        'L' => 'Large',
        'T' => 'Thumbnail',
        'S' => 'Small',
        'G' => 'Gallery'
    );

    public function _toHtml() {
        $html = parent::_toHtml();

        $types = array();
        
        $value = $this->getValue();
        
        if(!empty($value)) {
            $value = $value->getData();
            
            if(!empty($value)) {
                foreach ($value as $type) {
                    $types[] = $this->_types[$type];
                }
            }
        }
        
        $html .= implode(',',$types);
        
        return $html;
    }
}