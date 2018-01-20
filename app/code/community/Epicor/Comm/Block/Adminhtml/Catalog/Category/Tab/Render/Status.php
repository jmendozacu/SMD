<?php

class Epicor_Comm_Block_Adminhtml_Catalog_Category_Tab_Render_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
     public function render(Varien_Object $row) {
          $status = array(  1=>'Enabled',
                            2=>'Disabled',);
          return $status[$row->getStatus()];
    }
}

