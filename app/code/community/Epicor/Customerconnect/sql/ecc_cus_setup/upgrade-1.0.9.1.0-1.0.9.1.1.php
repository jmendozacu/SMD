<?php

$installer = $this;

$installer->startSetup();

$setup = new Mage_Core_Model_Config();

$data = 'a:7:{s:18:"_1501838519469_469";a:8:{s:6:"header";s:14:"Invoice Number";s:4:"type";s:4:"text";s:7:"options";s:0:"";s:5:"index";s:14:"invoice_number";s:9:"filter_by";s:3:"erp";s:9:"condition";s:2:"EQ";s:9:"sort_type";s:6:"number";s:8:"renderer";s:0:"";}s:18:"_1501839478956_956";a:8:{s:6:"header";s:12:"Invoice Date";s:4:"type";s:4:"date";s:7:"options";s:0:"";s:5:"index";s:12:"invoice_date";s:9:"filter_by";s:3:"erp";s:9:"condition";s:7:"LTE/GTE";s:9:"sort_type";s:4:"date";s:8:"renderer";s:0:"";}s:18:"_1501839503350_350";a:8:{s:6:"header";s:8:"Due Date";s:4:"type";s:4:"date";s:7:"options";s:0:"";s:5:"index";s:8:"due_date";s:9:"filter_by";s:3:"erp";s:9:"condition";s:7:"LTE/GTE";s:9:"sort_type";s:4:"date";s:8:"renderer";s:0:"";}s:18:"_1501839594519_519";a:8:{s:6:"header";s:14:"Invoice Amount";s:4:"type";s:6:"number";s:7:"options";s:0:"";s:5:"index";s:14:"original_value";s:9:"filter_by";s:3:"erp";s:9:"condition";s:7:"LTE/GTE";s:9:"sort_type";s:6:"number";s:8:"renderer";s:76:"Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_InvoiceAmount";}s:18:"_1501839629977_977";a:8:{s:6:"header";s:11:"Paid Amount";s:4:"type";s:6:"number";s:7:"options";s:0:"";s:5:"index";s:13:"payment_value";s:9:"filter_by";s:3:"erp";s:9:"condition";s:7:"LTE/GTE";s:9:"sort_type";s:6:"number";s:8:"renderer";s:75:"Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_PaymentValue";}s:18:"_1501849440455_455";a:8:{s:6:"header";s:15:"Invoice Balance";s:4:"type";s:6:"number";s:7:"options";s:0:"";s:5:"index";s:17:"outstanding_value";s:9:"filter_by";s:3:"erp";s:9:"condition";s:7:"LTE/GTE";s:9:"sort_type";s:6:"number";s:8:"renderer";s:77:"Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_InvoiceBalance";}s:18:"_1515397488450_450";a:8:{s:6:"header";s:7:"Ship To";s:4:"type";s:4:"text";s:7:"options";s:0:"";s:5:"index";s:35:"delivery_addresses_delivery_address";s:9:"filter_by";s:3:"erp";s:9:"condition";s:4:"LIKE";s:9:"sort_type";s:4:"text";s:8:"renderer";s:78:"Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_DeliveryAddress";}}';

$setup->saveConfig('customerconnect_enabled_messages/CAPS_request/grid_config', $data, 'default', 0); 

$installer->endSetup();