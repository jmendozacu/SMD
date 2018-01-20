<?php

/**
 * Version 1.0.0.106 to 1.0.0.107 upgrade
 * 
 * Added company value to oneline template
 */
$installer = $this;
$installer->startSetup();

$config_array = array(
    'oneline' => "{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}, {{depend company}}{{var company}}, {{/depend}}{{var street}}, {{var city}}, {{var region}} {{var postcode}}, {{var country}}"
);
$update_config = new Mage_Core_Model_Config();
foreach ($config_array as $key => $value) {
    $update_config->saveConfig("customer/address_templates/{$key}", $value);
}

$installer->endSetup();
