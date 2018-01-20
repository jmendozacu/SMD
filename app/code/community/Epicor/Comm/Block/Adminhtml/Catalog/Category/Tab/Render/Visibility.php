<?php

class Epicor_Comm_Block_Adminhtml_Catalog_Category_Tab_Render_Visibility extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
      public function render(Varien_Object $row) {
          $visibility = array(1=>'Not Visible Individually',
                             2=>'Catalog',
                             3=>'Search',
                             4=>'Catalog,Search');
          return $visibility[$row->getVisibility()];
    }
}

