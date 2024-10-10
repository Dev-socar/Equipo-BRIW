<?php

$config = parse_ini_file("path.ini");
define('RUTA', $config['RUTA']);


#Own 
#	js
$own_file_js = '<script src="' . RUTA . '/js/app.js"></script>';
#	css
$own_file_css = '<link rel="stylesheet" href="' . RUTA . '/dist/styles.min.css">';




$host = "localhost";     // Servidor de la base de datos
$usuario = "root";  // Usuario de la base de datos
$password = "";  // Contraseña del usuario
$base_datos = "northwind";  // Nombre de la base de datos
