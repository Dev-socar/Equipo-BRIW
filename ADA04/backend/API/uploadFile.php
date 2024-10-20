<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json'); // Definir la cabecera para devolver JSON

require '../config/conexion.php';
$db = conectarDB();

// Función para generar un hash único basado en el nombre del archivo y su contenido
function generarHash($filePath)
{
    return hash_file('sha256', $filePath); // Generar un hash SHA-256 del archivo
}

// Función para sanitizar el nombre de un archivo
function sanitizarNombreArchivo($nombreArchivo)
{
    return preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $nombreArchivo);
}

// Verificar si el método es POST y si se han enviado archivos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES)) {
    foreach ($_FILES as $file) {
        $nombreReal = sanitizarNombreArchivo($file['name']); // Nombre real del archivo, sanitizado
        $rutaTemporal = $file['tmp_name']; // Ruta temporal del archivo

        // Verificar que se haya subido un archivo
        if ($rutaTemporal === '') {
            echo json_encode(["success" => false, "message" => "Archivo no encontrado en la solicitud."]);
            exit;
        }

        // Verificar el tipo de archivo (solo permitir archivos de texto)
        if ($file['type'] !== 'text/plain') {
            echo json_encode(["success" => false, "message" => "Tipo de archivo no permitido."]);
            exit;
        }

        // Generar el hash del archivo
        $nombreHash = generarHash($rutaTemporal);

        // Definir la ruta de destino donde se guardará el archivo
        $rutaDestino = '../uploads/' . $nombreHash . '_' . time() . '.txt'; // Agrega timestamp para evitar colisiones
        if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
            // Insertar en la tabla Documentos el nombre real y el nombre hash
            $stmt = $db->prepare("INSERT INTO Documentos (nombre_real, nombre_hash) VALUES (?, ?)");
            $stmt->bind_param('ss', $nombreReal, $nombreHash);
            if ($stmt->execute()) {
                $idDocumento = $stmt->insert_id; // Obtener el ID del documento insertado
                $stmt->close();

                // Llamar a la función para indexar el archivo en la base de datos
                $resultadoIndexacion = indexarArchivo($rutaDestino, $nombreHash, $idDocumento, $db);

                if ($resultadoIndexacion) {
                    // Si la indexación fue exitosa, devolver un mensaje de éxito
                    echo json_encode(["success" => true, "message" => "Archivo subido e indexado correctamente."]);
                } else {
                    echo json_encode(["success" => false, "message" => "Error en la indexación del archivo."]);
                }
            } else {
                // Error al insertar en la tabla Documentos
                echo json_encode(["success" => false, "message" => "Error al insertar en la tabla Documentos."]);
            }
        } else {
            // Error al mover el archivo a la ruta destino
            echo json_encode(["success" => false, "message" => "Error al mover el archivo."]);
        }
    }
} else {
    // Método no permitido o archivo no enviado
    echo json_encode(["success" => false, "message" => "Método no permitido o archivo no recibido."]);
}

// Función para indexar el archivo en el índice invertido
function indexarArchivo($rutaArchivo, $nombreHash, $idDocumento, $db)
{
    // Leer el contenido del archivo
    $contenido = file_get_contents($rutaArchivo);

    // Limpiar el contenido, eliminar puntuaciones y pasar a minúsculas
    $contenido = strtolower(preg_replace('/[^\w\s]/', '', $contenido));

    // Dividir el contenido en palabras
    $palabras = preg_split('/\s+/', $contenido);

    // Arreglo para rastrear los términos que ya hemos procesado
    $terminosProcesados = [];

    // Recorrer cada palabra y actualizar el índice invertido
    foreach (array_count_values($palabras) as $palabra => $frecuencia) {
        // Evitar procesar palabras vacías
        if (strlen($palabra) > 0 && !in_array($palabra, $terminosProcesados)) {
            // Marcar el término como procesado
            $terminosProcesados[] = $palabra;

            // Verificar si el término ya existe en el diccionario
            $stmtDic = $db->prepare("SELECT id, numero_de_docs FROM Diccionario WHERE termino = ?");
            $stmtDic->bind_param('s', $palabra);
            $stmtDic->execute();
            $result = $stmtDic->get_result();

            if ($result->num_rows > 0) {
                // Si el término ya existe, actualizar el número de documentos y la frecuencia
                $row = $result->fetch_assoc();
                $idTermino = $row['id'];
                $numeroDeDocs = $row['numero_de_docs'] + 1;

                // Actualizar el número de documentos
                $updateStmt = $db->prepare("UPDATE Diccionario SET numero_de_docs = ? WHERE id = ?");
                $updateStmt->bind_param('ii', $numeroDeDocs, $idTermino);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                // Si el término no existe, insertarlo en el diccionario
                $insertStmt = $db->prepare("INSERT INTO Diccionario (termino, numero_de_docs) VALUES (?, 1)");
                $insertStmt->bind_param('s', $palabra);
                $insertStmt->execute();
                $idTermino = $insertStmt->insert_id;
                $insertStmt->close();
            }
            $stmtDic->close();

            // Extraer un fragmento de 50 caracteres alrededor del término (si es posible)
            $pos = strpos(strtolower($contenido), $palabra);
            $fragmento = substr($contenido, max(0, $pos - 25), 50);

            // Comprobar si ya existe un registro en la tabla Posting para este término y documento
            $stmtPostCheck = $db->prepare("SELECT frecuencia FROM Posting WHERE idTermino = ? AND documento = ?");
            $stmtPostCheck->bind_param('is', $idTermino, $nombreHash);
            $stmtPostCheck->execute();
            $resultPostCheck = $stmtPostCheck->get_result();

            if ($resultPostCheck->num_rows > 0) {
                // Si el registro ya existe, actualizar la frecuencia
                $rowPost = $resultPostCheck->fetch_assoc();
                $nuevaFrecuencia = $rowPost['frecuencia'] + $frecuencia;

                $updateStmt = $db->prepare("UPDATE Posting SET frecuencia = ?, fragmento = ? WHERE idTermino = ? AND documento = ?");
                $updateStmt->bind_param('isis', $nuevaFrecuencia, $fragmento, $idTermino, $nombreHash);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                // Si no existe, insertar un nuevo registro
                $stmtPost = $db->prepare("INSERT INTO Posting (idTermino, documento, frecuencia, fragmento) VALUES (?, ?, ?, ?)");
                $stmtPost->bind_param('isis', $idTermino, $nombreHash, $frecuencia, $fragmento);
                $stmtPost->execute();
                $stmtPost->close();
            }
            $stmtPostCheck->close();
        }
    }
    return true; // Retornar true si la indexación fue exitosa
}
