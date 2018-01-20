<?php

/**
 * Version 1.0.0.60 to 1.0.0.61 upgrade
 * 
 * Ensure 'description' is present in core_config_data path: 'epicor_comm_field_mapping/stt_mapping/product_ecommerce_description'
 */

$installer = $this;
$installer->startSetup();

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
            T: {{var telephone}}
            {{depend fax}}F: {{var fax}}{{/depend}}
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
            {{depend telephone}}<br/>T: {{var telephone}}{{/depend}}
            {{depend fax}}<br/>F: {{var fax}}{{/depend}}
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
            {{depend telephone}}T: {{var telephone}}{{/depend}}|
            {{depend fax}}<br/>F: {{var fax}}{{/depend}}|
            {{depend vat_id}}<br/>VAT: {{var vat_id}}{{/depend}}|",
   'js_template'=>"#{prefix} #{firstname} #{middlename} #{lastname} #{suffix}<br/>#{company}<br/>#{street0}<br/>#{street1}<br/>#{street2}<br/>#{street3}<br/>#{city}, #{region}, #{postcode}<br/>#{country_id}<br/>Email: #{email}<br/>T: #{telephone}<br/>F: #{fax}<br/>VAT: #{vat_id}",
   'oneline'=>"{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}, {{var street}}, {{var city}}, {{var region}} {{var postcode}}, {{var country}}"    
);
$update_config = new Mage_Core_Model_Config();
foreach($config_array as $key=>$value){
    $update_config->saveConfig("customer/address_templates/{$key}", $value);
}

$installer->endSetup();