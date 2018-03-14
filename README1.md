# Readme!

## Installing IonCube locally

Download Ioncube, unzip and move ioncube for PHP 5.6:

```
curl -O https://downloads.ioncube.com/loader_downloads/ioncube_loaders_dar_x86-64.zip
unzip ioncube_loaders_dar_x86-64.zip
cp ioncube_loader_dar_5.6.so /Applications/MAMP/bin/php/php5.6.27/modules/
```

Add following to *top* of php.ini config:

```
[ioncube]
zend_extension="/Applications/MAMP/bin/php/php5.6.27/modules/ioncube_loader_dar_5.6.so"
```

## Compiling CSS

This is dependant on compass from the rwd/default theme:

```
cd /skin/frontend/webtise/default/scss/
compass watch
```
