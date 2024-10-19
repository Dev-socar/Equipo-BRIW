<?php

function conectarDB(): mysqli
{
    //Conectar a la BD
    $db = mysqli_connect(
        'localhost',
        'root',
        '',
        'BRIW_ADA4'
    );

    // Establecer el conjunto de caracteres
    $db->set_charset('utf8');

    // Comprobar la conexión
    if (!$db) {
        die("Error: No se pudo conectar a MySQL. " .
            "Error número: " . mysqli_connect_errno() .
            " - " . mysqli_connect_error());
    }

    return $db;
}
