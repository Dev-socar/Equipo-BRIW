<?php

$config = parse_ini_file("path.ini");
define('RUTA', $config['RUTA']);
define('EUROPEANA_APIKEY', $config['EUROPEANA_APIKEY']);


#Own 
#	js
$own_file_js = '<script src="' . RUTA . '/js/app.js"></script>';
#	css
$own_file_css = '<link rel="stylesheet" href="' . RUTA . '/dist/styles.min.css">';
