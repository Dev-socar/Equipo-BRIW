<?php

header('Access-Control-Allow-Origin: *'); // Permitir cualquier origen
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // Métodos permitidos
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Encabezados permitidos
header('Content-Type: application/json'); // Configurar el tipo de contenido a JSON

require '../config/conexion.php';
$db = conectarDB();



