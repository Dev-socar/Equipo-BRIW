<?php

require("./Responses.php");

// Configuración de la conexión a la base de datos (ajusta según tus necesidades)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ada_06";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];

        // Ruta donde se guardará el archivo
        $uploadDirectory = 'uploads/';
        $nombreOriginal = basename($file['name']);
        $nombreHash = generarHash($nombreOriginal);
        $uploadFile = $uploadDirectory . $nombreHash;

        // Mover el archivo a la carpeta de destino
        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            // Leer el contenido del archivo
            $contenido = file_get_contents($uploadFile);

            // Preparar la sentencia SQL
            $stmt = $conn->prepare("INSERT INTO documentos (ruta_del_documento, nombre_real, nombre_hash, contenido) VALUES (?, ?, ?, ?)");
            $stmt->execute([$uploadFile, $nombreOriginal, $nombreHash, $contenido]);

            response('success', 'El archivo se ha subido y guardado en la base de datos correctamente.');
        } else {
            response('error', 'Error al subir el archivo.');
        }
    } else {
        response('error', 'No se ha recibido ningún archivo.');        
    }
} catch(PDOException $e) {
    response('error', 'Error de conexión a la base de datos: ' . $e->getMessage());
}

function generarHash($nombreOriginal) {
    return hash('sha256', $nombreOriginal . time()); // Combina el nombre original con un timestamp para mayor aleatoriedad
}