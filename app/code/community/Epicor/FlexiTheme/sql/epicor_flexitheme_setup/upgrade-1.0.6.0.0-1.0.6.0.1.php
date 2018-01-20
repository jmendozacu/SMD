<?php
/************************************************************************
Step : Update Translation Table to add is_custom column
*************************************************************************/
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$helper = Mage::helper('flexitheme/setup');
/* @var $helper Epicor_Flexitheme_Helper_Setup */

$helper->updateLayoutBlock('cart_sidebar', array(
    'block_template' => 'epicor_comm/checkout/cart/sidebar.phtml',
    'block_xml' => '
        <action method="addItemRender"><type>default</type><block>checkout/cart_item_renderer</block><template>epicor_comm/checkout/cart/sidebar/default.phtml</template></action>
        <action method="addItemRender"><type>simple</type><block>checkout/cart_item_renderer</block><template>epicor_comm/checkout/cart/sidebar/default.phtml</template></action>
        <action method="addItemRender"><type>grouped</type><block>checkout/cart_item_renderer_grouped</block><template>epicor_comm/checkout/cart/sidebar/default.phtml</template></action>
        <action method="addItemRender"><type>configurable</type><block>checkout/cart_item_renderer_configurable</block><template>epicor_comm/checkout/cart/sidebar/default.phtml</template></action>
        <action method="addItemRender"><type>bundle</type><block>bundle/checkout_cart_item_renderer</block><template>epicor_comm/checkout/cart/sidebar/default.phtml</template></action>'
));



$installer->endSetup();