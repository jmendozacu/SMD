<?php

/**
 * Version 1.0.6.6.0 - 1.0.6.6.1 upgrade
 * 
 * Removes the locale folder from base/default following change to file location for translate.csv
 */
$baseDefaultLocale = Mage::getBaseDir('app').DS."design".DS."frontend".DS."base".DS."default".DS."locale".DS;
$appLocale = Mage::getBaseDir('app').DS.'locale';
if (file_exists($baseDefaultLocale)){           // if base default locale directory exists, copy to app folder and then delete base default 
    shell_exec("rsync -ar {$baseDefaultLocale} {$appLocale}");
    shell_exec("rm -rf {$baseDefaultLocale} ");
}            
            
