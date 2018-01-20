<?php

/**
 * Version 1.0.0.6 to 1.0.0.7 upgrade
 * 
 * Fixes cart sidebar flexitheme xml
 */

$layout_block = Mage::getModel('flexitheme/layout_block')->load('checkout/cart_sidebar','block_type');
/* @var $layout_block FlexiTheme_Model_Layout_Block */

$block_xml = '
                <action method="addItemRender"><type>simple</type><block>checkout/cart_item_renderer</block><template>checkout/cart/sidebar/default.phtml</template></action>
                <action method="addItemRender"><type>grouped</type><block>checkout/cart_item_renderer_grouped</block><template>checkout/cart/sidebar/default.phtml</template></action>
                <action method="addItemRender"><type>configurable</type><block>checkout/cart_item_renderer_configurable</block><template>checkout/cart/sidebar/default.phtml</template></action>
                <block type="core/text_list" name="cart_sidebar.extra_actions" as="extra_actions" translate="label" module="checkout">
                    <label>Shopping Cart Sidebar Extra Actions</label>
                </block>
';

$layout_block->setBlockXml($block_xml);
$layout_block->save();