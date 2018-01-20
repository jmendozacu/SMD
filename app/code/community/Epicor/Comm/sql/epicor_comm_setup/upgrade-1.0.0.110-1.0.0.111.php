<?php

/**
 * Upgrade - 1.0.0.110  to 1.0.0.111
 * 
 * Add mobile number to customer_address and sales/quote address
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->removeAttribute('customer_address', 'mobile_phone');
$setup->addAttribute('customer_address', 'mobile_number', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'input' => 'text',
    'label' => 'Mobile Number',
    'nullable' => true,
    'global' => 1,
    'visible' => 1,   
    'required' => 0,
    'user_defined' => 1,
    'default' => ' ',
    'visible_on_front' => 1,
));

Mage::getSingleton('eav/config')
        ->getAttribute('customer_address', 'mobile_number')
        ->setData('used_in_forms', array(
            'adminhtml_customer_address',
            'customer_address_edit',
            'customer_register_address'
        ))
        ->save();

//// Quote / Order table  
//
$conn->addColumn($installer->getTable('sales/quote_address'), 'mobile_number', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '15',
    'comment' => 'Mobile Phone'
));

$conn->addColumn($installer->getTable('sales/order_address'), 'mobile_number', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '15',
    'comment' => 'Mobile Phone'
));

//customer_erpaccount_address

$conn->addColumn($installer->getTable('epicor_comm/customer_erpaccount_address'), 'mobile_number', array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '15',
    'comment' => 'Mobile Phone'
));

//config array
$config_array = array(
    'text'=>"{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}
                        {{depend company}}{{var company}}{{/depend}}
                        {{if street1}}{{var street1}}
                        {{/if}}
                        {{depend street2}}{{var street2}}{{/depend}}
                        {{depend street3}}{{var street3}}{{/depend}}
                        {{depend street4}}{{var street4}}{{/depend}}
                        {{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}
                        {{var country}}
                        {{depend email}}Email: {{var email}}{{/depend}}
                        Tel: {{var telephone}}
                        {{depend mobile_number}}Mob: {{var mobile_number}}{{/depend}}
                        {{depend fax}}Fax: {{var fax}}{{/depend}}					
                        {{depend vat_id}}VAT: {{var vat_id}}{{/depend}}",
   'html'=>"{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}<br/>
                        {{depend company}}{{var company}}<br />{{/depend}}
                        {{if street1}}{{var street1}}<br />{{/if}}
                        {{depend street2}}{{var street2}}<br />{{/depend}}
                        {{depend street3}}{{var street3}}<br />{{/depend}}
                        {{depend street4}}{{var street4}}<br />{{/depend}}
                        {{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}<br/>
                        {{var country}}
                        {{depend email}}<br/>Email: {{var email}}{{/depend}}
                        {{depend telephone}}<br/>Tel: {{var telephone}}{{/depend}}
                        {{depend mobile_number}}<br/>Mob: {{var mobile_number}}{{/depend}}
                        {{depend fax}}<br/>Fax: {{var fax}}{{/depend}}
                        {{depend vat_id}}<br/>VAT: {{var vat_id}}{{/depend}}",
   'pdf'=>"{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}|
                        {{depend company}}{{var company}}|{{/depend}}
                        {{if street1}}{{var street1}}
                        {{/if}}
                        {{depend street2}}{{var street2}}|{{/depend}}
                        {{depend street3}}{{var street3}}|{{/depend}}
                        {{depend street4}}{{var street4}}|{{/depend}}
                        {{if city}}{{var city}},|{{/if}}
                        {{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}|
                        {{var country}}|
                        {{depend email}}Email: {{var email}}{{/depend}}|
                        {{depend telephone}}Tel: {{var telephone}}{{/depend}}|
                        {{depend mobile_number}}Mob: {{var mobile_number}}{{/depend}}|
                        {{depend fax}}<br/>Fax: {{var fax}}{{/depend}}|
                        {{depend vat_id}}<br/>VAT: {{var vat_id}}{{/depend}}|",
   'js_template'=>"#{prefix} #{firstname} #{middlename} #{lastname} #{suffix}<br/>#{company}<br/>#{street0}<br/>#{street1}<br/>#{street2}<br/>#{street3}<br/>#{city}, #{region}, #{postcode}<br/>#{country_id}<br/>Email: #{email}<br/>Tel: #{telephone}<br/>Mob: #{mobile_number}<br/>Fax: #{fax}<br/>VAT: #{vat_id}",
   );

$update_config = new Mage_Core_Model_Config();
foreach($config_array as $key=>$value){
        $update_config->saveConfig("customer/address_templates/{$key}", $value);
}


$installer->endSetup();
