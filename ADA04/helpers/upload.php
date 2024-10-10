<?php
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Ruta donde se guardará el archivo
    $uploadDirectory = 'uploads/';
    $uploadFile = $uploadDirectory . basename($file['name']);

    // Crear el directorio si no existe
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    // Mover el archivo cargado a la ubicación de destino
    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
        echo "El archivo se ha subido correctamente.";
    } else {
        echo "Error al subir el archivo.";
    }
} else {
    echo "No se ha recibido ningún archivo.";
}
?>